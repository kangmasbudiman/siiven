<header id="header" class="header">

    <div class="header-menu">

        <div class="col-sm-7">
            <a id="menuToggle" class="menutoggle pull-left"><i class="fa fa fa-tasks"></i></a>

            <div class="header-left d-flex align-items-center gap-2">

                {{-- Notifikasi Stok Habis (Admin & Gudang) --}}
                @can('isAdminGudang')
                <div class="dropdown for-notification">
                    <button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bell"></i>
                        <span class="count bg-danger" style="width: auto; height: auto; padding: 2px 6px; right: {{ $limit > 10 ? '-10px' : 0 }}">{{ $limit }}</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg border">
                        <a href="{{ route('notification') }}" class="dropdown-item media bg-danger text-white">
                            <i class="fa fa-exclamation-triangle"></i>
                            {{ $limit }} produk kehabisan stok
                        </a>
                    </div>
                </div>
                @endcan

                {{-- Notifikasi Usulan Pending (Approver: Kabag / Direktur / Ka.Keuangan / Bendahara) --}}
                @if(auth()->user()->approval_level)
                <div class="dropdown for-notification">
                    <button id="notif-btn-approver"
                            class="btn {{ $pendingApproval > 0 ? 'btn-warning' : 'btn-secondary' }} dropdown-toggle"
                            data-toggle="dropdown">
                        <i class="fa fa-file-text-o"></i>
                        <span id="notif-badge-count"
                              class="count {{ $pendingApproval > 0 ? 'bg-danger' : 'bg-secondary' }}"
                              style="width: auto; height: auto; padding: 2px 6px;">
                            {{ $pendingApproval }}
                        </span>
                    </button>
                    <div id="notif-dropdown-menu" class="dropdown-menu dropdown-menu-lg border" style="min-width: 280px;">
                        {{-- Header: selalu ada, JS yang show/hide --}}
                        <div id="notif-dropdown-header" class="dropdown-item bg-warning text-dark fw-bold"
                             style="pointer-events:none; display:{{ $pendingApproval > 0 ? 'block' : 'none' }};">
                            <i class="fa fa-clock-o me-1"></i>
                            <span id="notif-dropdown-count">{{ $pendingApproval }}</span> usulan menunggu persetujuan Anda
                        </div>
                        <div id="notif-dropdown-divider-top" class="dropdown-divider m-0"
                             style="display:{{ $pendingApproval > 0 ? 'block' : 'none' }};"></div>

                        {{-- Items --}}
                        <div id="notif-dropdown-items">
                            @if($pendingApproval > 0)
                                @php
                                    $statusMap   = [1=>'diajukan',2=>'diperiksa',3=>'dikonfirmasi',4=>'diketahui'];
                                    $status      = $statusMap[auth()->user()->approval_level] ?? null;
                                    $previewList = $status
                                        ? \App\Models\UsulanPembelian::with('ruangan')->where('status',$status)->latest()->limit(5)->get()
                                        : collect();
                                @endphp
                                @foreach($previewList as $up)
                                <a href="{{ route('usulan.show', $up->id) }}" class="dropdown-item py-2">
                                    <div class="fw-semibold" style="font-size:13px;">{{ $up->nomor_usulan }}</div>
                                    <div class="text-muted" style="font-size:11px;">{{ $up->ruangan->nama_ruangan ?? '-' }} &mdash; {{ $up->tanggal_pengajuan->format('d/m/Y') }}</div>
                                </a>
                                @endforeach
                            @else
                                <div class="dropdown-item text-muted text-center py-3">
                                    <i class="fa fa-check-circle text-success me-1"></i>
                                    Tidak ada usulan pending
                                </div>
                            @endif
                        </div>

                        {{-- Footer: selalu ada, JS yang show/hide --}}
                        <div id="notif-dropdown-divider-bot" class="dropdown-divider m-0"
                             style="display:{{ $pendingApproval > 0 ? 'block' : 'none' }};"></div>
                        <a id="notif-dropdown-footer" href="{{ route('usulan.index') }}"
                           class="dropdown-item text-center text-primary" style="font-size:13px;display:{{ $pendingApproval > 0 ? 'block' : 'none' }};">
                            Lihat Semua Usulan &rarr;
                        </a>
                    </div>
                </div>
                @endif

            </div>
        </div>

        <div class="col-sm-5">
            <div class="user-area dropdown float-right">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-user mt-2"></i>
                </a>

                <div class="user-menu dropdown-menu">
                    <span>{{ Auth::user()->nama }}</span>

                    <div class="dropdown-divider"></div>

                    <a class="nav-link" href="{{ route('change_password') }}"><i class="fa fa-key"></i> Ganti Password</a>
                    @can('isAdmin')
                    <a class="nav-link" href="{{ route('setting.index') }}"><i class="fa fa-cog"></i> Settings</a>
@endcan
                    <a class="nav-link" href="{{ route('logout') }}"><i class="fa fa-power-off"></i> Logout</a>
                </div>
            </div>

        </div>
    </div>

</header>