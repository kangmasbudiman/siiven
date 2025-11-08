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
