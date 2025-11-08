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

                @can('isAdmin')
                    <li class="{{ active('/') }}">
                        <a href="{{ route('home') }}"> <i class="menu-icon fa fa-dashboard"></i>Dashboard</a>
                    </li>
                     <li class="">
                        <a href="{{ route('stockapp.indexadmin') }}"> <i class="menu-icon fa fa-archive"></i>Stok Aplikasi</a>
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
                                <i class="menu-icon fa fa-clock-o"></i>
                                <a href="{{ route('shift.index') }}">Shift</a>
                            </li>

                              <li>
                                <i class="menu-icon fa fa-cog"></i>
                                <a href="{{ route('aplikasi.index') }}">Data Aplikasi</a>
                            </li>
                              <li>
                                <i class="menu-icon fa fa-university"></i>
                                <a href="{{ route('bank.index') }}">Data Bank</a>
                            </li>
                            
                              <li>
                                <i class="menu-icon fa fa-history"></i>
                                <a href="{{ route('user.index') }}">Aktivitas System</a>
                            </li>
                              <li>
                                <i class="menu-icon fa fa-briefcase"></i>
                                <a href="{{ route('reseller.index') }}">Reseller</a>
                            </li>

                              <li>
                                <i class="menu-icon fa fa-clock-o"></i>
                                <a href="{{ route('usershift.index') }}">User Shift</a>
                            </li>


                          
                          
                        </ul>
                    </li>
               
                @endcan
                @can('isKasir')
                    <li
                        class="menu-item-has-children dropdown {{ active('transaction', 'group', 'active') }} {{ active('detail_transaction', 'group', 'active') }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="menu-icon fa fa-tasks"></i>
                            Transaksi
                        </a>
                        <ul class="sub-menu children dropdown-menu">

                            <li>
                                <i class="menu-icon fa fa-credit-card"></i>
                                <a href="{{ route('stockapp.index') }}">TopUp/Reload</a>
                            </li>

                            <li>
                                <i class="menu-icon fa fa-money"></i>
                                <a href="{{ route('transaction.index') }}">Transaksi</a>
                            </li>
                            <li>
                                <i class="menu-icon fa fa-history"></i>
                                <a href="{{ route('shiftsession.history') }}">Session Hystory</a>
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
                            Laporan
                        </a>
                        <ul class="sub-menu children dropdown-menu">
                            @can('isAdmin')
                                <li>
                                    <i class="menu-icon fa fa-file"></i>
                                    <a href="{{ route('shiftsession.listshiftsession') }}">Laporan Shift</a>
                                </li>
                                <li>
                                    <i class="menu-icon fa fa-file"></i>
                                    <a href="{{ route('transaction.laporanperiode') }}">Laporan Periode</a>
                                </li>
                               
                            @endcan
                           
                           
                        </ul>
                    </li>
                @endcan



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
