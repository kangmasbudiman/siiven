@extends('_layouts.app')

@section('title', 'Manajemen Stok Aplikasi')

@section('content')
    <div class="container-fluid mt-4">

        <h4 class="mb-3">📦 Manajemen Mutasi/Riwayat Stok Aplikasi </h4>


        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <strong>Daftar Aplikasi & Stok</strong>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Aplikasi</th>
                            <th>Coin Type</th>
                            <th>Rate</th>
                            <th>Stok Saat Ini</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($apps as $app)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $app->nameaplication }}</td>
                                <td>{{ $app->coin_type }}</td>
                                <td>{{ $app->rate }}</td>
                                <td><strong class="text-primary">{{ $app->qty }}</strong></td>
                                <td>
                                    <button class="btn btn-success btn-sm btn-reload" data-app-id="{{ $app->id }}"
                                        data-app-name="{{ $app->nameaplication }}">
                                        Reload
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Filter Periode -->
        <div class="card shadow mb-4">
            <div class="card-header bg-secondary text-white">
                <strong>Filter Riwayat Perubahan Stok</strong>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('stockapp.indexadmin') }}" class="form-inline">
                    <div class="form-group mr-3">
                        <label for="start_date" class="mr-2">Dari:</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                            value="{{ request('start_date') }}">
                    </div>
                    <div class="form-group mr-3">
                        <label for="end_date" class="mr-2">Sampai:</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                            value="{{ request('end_date') }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('stockapp.indexadmin') }}" class="btn btn-outline-secondary ml-2">Reset</a>
                </form>

            

      
                    
                </form>

               

            </div>
        </div>


            <div class="card shadow mb-4">
            <div class="card-header bg-secondary text-white">
                <strong>Export PDF</strong>
            </div>
            <div class="card-body">
                <!-- Tombol Export PDF -->
                    <form method="GET" action="{{ route('stockapp.pdf') }}" class="form-inline">
                    <div class="form-group mr-3">
                        <label for="start_date" class="mr-2">Dari:</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                            value="{{ request('start_date') }}">
                    </div>
                    <div class="form-group mr-3">
                        <label for="end_date" class="mr-2">Sampai:</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                            value="{{ request('end_date') }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Export PDF</button>
                    <a href="{{ route('stockapp.indexadmin') }}" class="btn btn-outline-secondary ml-2">Reset</a>
                </form>



               

            </div>
        </div>


           
     



        <!-- Riwayat -->
        <div class="card shadow">
            <div class="card-header bg-light">
                <strong>🕒 Riwayat Perubahan Stok Terakhir</strong>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    @foreach ($logs as $log)
                        <li class="list-group-item">
                            <strong>{{ $log->aplikasi->nameaplication }}</strong><br>
                            {{ $log->type }} — {{ $log->amount }} coin oleh {{ $log->user->nama }}
                            <small class="text-muted float-right">{{ $log->created_at->format('d M Y H:i') }}</small>
                            @if ($log->note)
                                <br><small class="text-muted">{{ $log->note }}</small>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>

    <!-- Modal Reload -->
    <div class="modal fade" id="reloadModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('stockapp.reload') }}" method="POST">
                @csrf
                <div class="modal-content">

                    <input type="hidden" name="app_id" id="reload_app_id">

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Aplikasi</label>
                            <input type="text" class="form-control" id="reload_app_name" readonly>
                        </div>

                        <div class="form-group mt-3">
                            <label>Jumlah Reload</label>
                            <input type="number" class="form-control" name="reload_amount" required>
                        </div>
                        <div class="form-group mt-3">
                            <label>Notes</label>
                            <input type="text" class="form-control" name="note" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan Reload</button>
                    </div>
                </div>
            </form>
        </div>
    </div>





@endsection

@section('scripts')

@endsection
