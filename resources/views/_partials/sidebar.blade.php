<aside id="left-panel" class="left-panel">
    <nav class="navbar navbar-expand-sm navbar-default">

        <div class="navbar-header">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu"
                aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa fa-bars"></i>
            </button>
            <a class="navbar-brand" href="{{ route('home') }}">{{ site('nama_toko') }}</a>
        </div>

        <div id="main-menu" class="main-menu collapse navbar-collapse">
            <ul class="nav navbar-nav">

                {{-- Dashboard untuk approver --}}
                @if(auth()->user()->approval_level)
                    <li class="{{ active('/') }}">
                        <a href="{{ route('home') }}">
                            <i class="menu-icon fa fa-dashboard"></i>Dashboard
                            @php
                                $statusMapSidebar = [1=>'diajukan',2=>'diperiksa',3=>'dikonfirmasi',4=>'diketahui'];
                                $stSidebar = $statusMapSidebar[auth()->user()->approval_level] ?? null;
                                $pendingSidebar = $stSidebar ? \App\Models\UsulanPembelian::where('status',$stSidebar)->count() : 0;
                            @endphp
                            @if($pendingSidebar > 0)
                                <span class="badge bg-warning text-dark" style="font-size:10px;">{{ $pendingSidebar }}</span>
                            @endif
                        </a>
                    </li>
                @endif

                @if(!auth()->user()->approval_level)

                @can('isAdmin')
                    <li class="{{ active('/') }}">
                        <a href="{{ route('home') }}"> <i class="menu-icon fa fa-dashboard"></i>Dashboard</a>
                    </li>
                @endcan

                @can('isAdminGudang')
                    <li
                        class="menu-item-has-children dropdown {{ active('stuff', 'group', 'active') }} {{ active('rack', 'group', 'active') }} {{ active('distributor', 'group', 'active') }} {{ active('category', 'group', 'active') }} {{ active('barcode', 'group', 'active') }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="menu-icon fa fa-table"></i>
                            Master Data
                        </a>
                        <ul class="sub-menu children dropdown-menu">

                            <li>
                                <i class="menu-icon fa fa-user"></i>
                                <a href="{{ route('user.index') }}">Pengguna</a>
                            </li>

                              <li>
                                <i class="menu-icon fa fa-building"></i>
                                <a href="{{ route('ruangan.index') }}">Ruangan</a>
                            </li>

                             <li>
                                <i class="menu-icon fa fa-heartbeat"></i>
                                <a href="{{ route('kondisi.index') }}">Kondisi Barang</a>
                            </li>
                             <li>
                                <i class="menu-icon fa fa-tags"></i>
                                <a href="{{ route('kategori.index') }}">Kategori Barang</a>
                            </li>









                        </ul>
                    </li>
                @endcan
                @can('isKasir')
                    <li
                        class="menu-item-has-children dropdown {{ active('transaction', 'group', 'active') }} {{ active('detail_transaction', 'group', 'active') }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="menu-icon fa fa-tasks"></i>
                            Master Data
                        </a>
                        <ul class="sub-menu children dropdown-menu">

                            <li>
                                <i class="menu-icon fa fa-credit-card"></i>
                                <a href="{{ route('barang.index') }}">Data Barang</a>
                            </li>

                             <li>
                                <i class="menu-icon fa fa-credit-card"></i>
                                <a href="{{ route('inventory.index') }}">Inventory Ruangan</a>
                            </li>

                         


                            <li>

                                @can('isAdmin')
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                <!--
                @can('isAdmin')
    <li
                            class="menu-item-has-children dropdown {{ active('finance', false, 'active') }} {{ active('category_finance', false, 'active') }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">

                                <i class="menu-icon fa fa-print"></i>
                                Pengeluaran
                            </a>
                            <ul class="sub-menu children dropdown-menu">
                                <li>
                                    <i class="menu-icon fa fa-print"></i>
                                    <a href="{{ route('category_finance.index') }}"></i>Kategori Pengeluaran</a>
                                </li>
                                <li>
                                    <i class="menu-icon fa fa-print"></i>
                                    <a href="{{ route('finance.index') }}"></i>Pengeluaran</a>
                                </li>
                            </ul>
                        </li>
@endcan
                -->



                @can('isAdmin')
                    <li
                        class="menu-item-has-children dropdown {{ active('report/transaction') }} {{ active('report/expend') }} {{ active('report/stock') }} {{ active('report/sell') }} {{ active('report/buy') }} {{ active('report/ppn') }} {{ active('report/opname') }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="menu-icon fa fa-file"></i>
                            Laporan Inventaris
                        </a>
                        <ul class="sub-menu children dropdown-menu">
                            @can('isAdmin')
                                <li>
                                    <i class="menu-icon fa fa-file"></i>
                                    <a href="{{ route('laporan.index') }}">Data Barang</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @endif {{-- end non-approver menus --}}

                {{-- Menu Usulan Pembelian (semua user yang login) --}}
                <li class="menu-item-has-children dropdown {{ active('usulan-pembelian', 'group', 'active') }}">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="menu-icon fa fa-file-text-o"></i>
                        Usulan Pembelian
                        @php
                            $pendingApproval = 0;
                            if(auth()->check() && auth()->user()->approval_level) {
                                $statusMap = [1=>'diajukan',2=>'diperiksa',3=>'dikonfirmasi',4=>'diketahui'];
                                $st = $statusMap[auth()->user()->approval_level] ?? null;
                                if($st) $pendingApproval = \App\Models\UsulanPembelian::where('status',$st)->count();
                            }
                        @endphp
                        @if($pendingApproval > 0)
                            <span class="badge bg-danger" style="font-size:10px;">{{ $pendingApproval }}</span>
                        @endif
                    </a>
                    <ul class="sub-menu children dropdown-menu">
                        <li>
                            <i class="menu-icon fa fa-list"></i>
                            <a href="{{ route('usulan.index') }}">Daftar Usulan</a>
                        </li>
                        @if(!auth()->user()->approval_level)
                        <li>
                            <i class="menu-icon fa fa-plus"></i>
                            <a href="{{ route('usulan.create') }}">Buat Usulan Baru</a>
                        </li>
                        @endif
                    </ul>
                </li>



                <li
                    class="menu-item-has-children dropdown {{ active('change_password', false, 'active') }} {{ active('ppn', false, 'active') }} {{ active('setting', false, 'active') }} {{ active('user', 'group', 'active') }}">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="menu-icon fa fa-cog"></i>
                        Pengaturan
                    </a>
                    <ul class="sub-menu children dropdown-menu">
                        <li>
                            <i class="menu-icon fa fa-key"></i>
                            <a href="{{ route('change_password') }}">Ganti Password</a>
                        </li>
                        @can('isAdmin')
                            <li>
                                <i class="menu-icon fa fa-cog"></i>
                                <a href="{{ route('setting.index') }}">Nama Aplikasi</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </nav>
</aside>
