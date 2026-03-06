<!doctype html>
<html class="no-js" lang="en">

<head>

    @include('_includes.head')

</head>

<body class="open">

    <!-- Left Panel -->

    @include('_partials.sidebar')

    <!-- /#left-panel -->

    <!-- Left Panel -->

    <!-- Right Panel -->

    <div id="right-panel" class="right-panel">

        <!-- Header-->

        <x-navbar />

        <!-- /header -->
        <!-- Header-->

        <div class="breadcrumbs">
            <div class="col">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>@yield('title')</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="content mt-3">

            @yield('content')

        </div> <!-- .content -->
    </div><!-- /#right-panel -->

    <!-- Right Panel -->

    @include('_includes/foot')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        // pastikan jQuery global aktif
        window.$ = window.jQuery = jQuery;
        console.log("✅ jQuery aktif versi:", $.fn.jquery);
    </script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom Alert Component -->
    @include('components.alert')

    <!-- ✅ Gunakan yield agar section('scripts') bisa muncul -->
    @yield('scripts')

    <!-- Custom Scripts -->
    @stack('scripts')

    @auth
    @if(auth()->user()->approval_level)
    {{-- Polling notifikasi suara untuk approver --}}
    <script>
    (function () {
        const POLL_INTERVAL = 30000;
        const API_URL  = '{{ route("api.pending.count") }}';
        const UID      = '{{ auth()->user()->idUser }}';
        const KEY_PREV = 'notif_prev_' + UID; // sessionStorage: deteksi item baru untuk reload dashboard

        let prevCount  = null;
        let audioReady = false;   // true setelah user gesture pertama
        let pendingPlay = false;  // antrian bunyi menunggu gesture
        // toastDismissed: in-memory saja (reset setiap halaman dimuat = notif berulang)
        let toastDismissed = false;

        // ── Audio ────────────────────────────────────────────────────
        let _ctx = null;
        function getCtx() {
            if (!_ctx) try { _ctx = new (window.AudioContext || window.webkitAudioContext)(); } catch(e) {}
            return _ctx;
        }

        function playTones() {
            const ctx = getCtx();
            if (!ctx) return;
            try {
                const t = ctx.currentTime;
                [[880,t,0.18],[1100,t+.22,0.18],[880,t+.48,0.22]].forEach(([f,s,d]) => {
                    const o = ctx.createOscillator(), g = ctx.createGain();
                    o.connect(g); g.connect(ctx.destination);
                    o.type = 'sine'; o.frequency.value = f;
                    g.gain.setValueAtTime(0.5, s);
                    g.gain.exponentialRampToValueAtTime(0.001, s+d);
                    o.start(s); o.stop(s+d);
                });
            } catch(e) {}
        }

        function unlockAndPlay() {
            const ctx = getCtx();
            if (!ctx) return;
            ctx.resume().then(() => {
                audioReady = true;
                removeBanner();
                if (pendingPlay) { pendingPlay = false; playTones(); }
            }).catch(() => {});
        }

        // Daftarkan unlock pada gesture pengguna
        ['click','keydown','touchstart'].forEach(ev =>
            document.addEventListener(ev, function onGesture() {
                if (audioReady) return;
                unlockAndPlay();
            })
        );

        function playSound() {
            const ctx = getCtx();
            if (!ctx) return;
            if (ctx.state === 'running') {
                audioReady = true;
                playTones();
            } else {
                // Belum ada gesture → antri & tampilkan banner
                pendingPlay = true;
                showBanner();
                // Coba resume; kalau browser tolak, tunggu gesture
                ctx.resume().then(() => {
                    audioReady = true;
                    removeBanner();
                    if (pendingPlay) { pendingPlay = false; playTones(); }
                }).catch(() => {});
            }
        }

        // ── Banner "Aktifkan Suara" ───────────────────────────────────
        function showBanner() {
            if (document.getElementById('notif-sound-banner')) return;
            const b = document.createElement('div');
            b.id = 'notif-sound-banner';
            Object.assign(b.style, {
                position:'fixed', top:'68px', right:'16px', zIndex:'99998',
                background:'#e67e22', color:'#fff',
                padding:'10px 16px', borderRadius:'8px',
                boxShadow:'0 4px 14px rgba(0,0,0,0.35)',
                fontSize:'13px', fontWeight:'600',
                cursor:'pointer', userSelect:'none'
            });
            b.innerHTML = '🔔 Klik di sini untuk aktifkan notifikasi suara';
            b.onclick = () => { unlockAndPlay(); };
            document.body.appendChild(b);
        }

        function removeBanner() {
            const b = document.getElementById('notif-sound-banner');
            if (b) b.remove();
        }

        // ── Badge ────────────────────────────────────────────────────
        function updateBadge(count) {
            const badge = document.getElementById('notif-badge-count');
            const btn   = document.getElementById('notif-btn-approver');
            if (badge) badge.textContent = count;
            if (btn) {
                btn.classList.toggle('btn-warning',   count > 0);
                btn.classList.toggle('btn-secondary', count === 0);
            }
        }

        // ── Dropdown Content ─────────────────────────────────────────
        function updateDropdown(count, items) {
            const show = function(id, visible) {
                const el = document.getElementById(id);
                if (el) el.style.display = visible ? 'block' : 'none';
            };
            const countEl = document.getElementById('notif-dropdown-count');
            const itemsEl = document.getElementById('notif-dropdown-items');
            if (countEl) countEl.textContent = count;

            show('notif-dropdown-header',      count > 0);
            show('notif-dropdown-divider-top', count > 0);
            show('notif-dropdown-divider-bot', count > 0);
            show('notif-dropdown-footer',      count > 0);

            if (!itemsEl) return;
            if (count === 0) {
                itemsEl.innerHTML = '<div class="dropdown-item text-muted text-center py-3"><i class="fa fa-check-circle text-success me-1"></i>Tidak ada usulan pending</div>';
                return;
            }
            itemsEl.innerHTML = items.map(function(u) {
                return '<a href="' + u.url + '" class="dropdown-item py-2">'
                    + '<div class="fw-semibold" style="font-size:13px;">' + u.nomor + '</div>'
                    + '<div class="text-muted" style="font-size:11px;">' + u.ruangan + ' &mdash; ' + u.tanggal + '</div>'
                    + '</a>';
            }).join('');
        }

        // ── Toast ────────────────────────────────────────────────────
        function showToast(count) {
            if (toastDismissed) return;
            // Kalau toast sudah ada, update count-nya saja
            const existing = document.getElementById('notif-toast');
            if (existing) {
                const span = existing.querySelector('.notif-count');
                if (span) span.textContent = count;
                return;
            }
            const el = document.createElement('div');
            el.id = 'notif-toast';
            Object.assign(el.style, {
                position:'fixed', bottom:'28px', right:'28px', zIndex:'99999',
                background:'#c0392b', color:'#fff',
                padding:'16px 22px', borderRadius:'12px',
                boxShadow:'0 8px 28px rgba(0,0,0,0.4)',
                fontSize:'14px', fontWeight:'600',
                cursor:'pointer', maxWidth:'340px', lineHeight:'1.6',
                transition:'opacity 0.4s', userSelect:'none'
            });
            el.innerHTML =
                '🔔 <strong><span class="notif-count">' + count + '</span> usulan</strong> menunggu persetujuan Anda'
                + '<br><span style="font-weight:400;font-size:12px;opacity:.85;">Klik di sini untuk membaca →</span>';

            el.onclick = () => { window.location.href = '{{ route("usulan.index") }}'; };
            document.body.appendChild(el);

            const closeBtn = document.createElement('span');
            closeBtn.innerHTML = ' &times;';
            closeBtn.style.cssText = 'float:right;margin-left:8px;opacity:.7;font-size:18px;line-height:1;';
            closeBtn.onclick = (e) => {
                e.stopPropagation();
                toastDismissed = true; // hanya berlaku di sesi halaman ini
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 400);
            };
            el.prepend(closeBtn);
        }

        function hideToast() {
            const t = document.getElementById('notif-toast');
            if (t) { t.style.opacity = '0'; setTimeout(() => t.remove(), 400); }
        }

        // ── Polling ──────────────────────────────────────────────────
        function poll() {
            fetch(API_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    const count = parseInt(data.count) || 0;
                    updateBadge(count);
                    updateDropdown(count, data.items || []);

                    // Kalau sedang di halaman usulan → reset toast dismissed
                    if (window.location.pathname.startsWith('/usulan-pembelian')) {
                        toastDismissed = true; // sembunyikan toast selama di halaman ini
                        hideToast();
                        sessionStorage.setItem(KEY_PREV, count);
                        prevCount = count;
                        return;
                    }

                    if (count > 0 && !toastDismissed) {
                        playSound();
                        showToast(count);
                    } else if (count === 0) {
                        hideToast();
                        removeBanner();
                    }

                    // Reload dashboard kalau ada item BARU
                    if (prevCount !== null && count > prevCount && window.location.pathname === '/') {
                        sessionStorage.setItem(KEY_PREV, count);
                        prevCount = count;
                        setTimeout(() => location.reload(), 2000);
                        return;
                    }

                    sessionStorage.setItem(KEY_PREV, count);
                    prevCount = count;
                })
                .catch(() => {});
        }

        // Script ada di akhir body → DOM sudah siap, langsung eksekusi
        prevCount = parseInt(sessionStorage.getItem(KEY_PREV) || '0');
        poll();
        setInterval(poll, POLL_INTERVAL);
    })();
    </script>
    @endif
    @endauth

    <script>
document.addEventListener("DOMContentLoaded", function() {
    console.log("✅ DOM siap");

    // pakai native JS
    document.querySelectorAll('[data-toggle="modal"][data-target="#modalEditTransaction"]').forEach(btn => {
        btn.addEventListener("click", function() {
            console.log("🟢 Tombol Edit diklik (native JS)");
        });
    });

    // pakai jQuery kalau aktif
    if (typeof jQuery !== 'undefined') {
        console.log("✅ jQuery aktif versi:", jQuery.fn.jquery);
        jQuery(document).on("click", '[data-toggle="modal"][data-target="#modalEditTransaction"]', function() {
            console.log("🟢 Tombol Edit diklik (pakai jQuery)");
        });
    } else {
        console.warn("⚠️ jQuery belum aktif!");
    }
});
</script>


</body>

</html>
