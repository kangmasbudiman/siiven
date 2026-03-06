@extends('_layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')
	
	<div class="row justify-content-center">
		<div class="col-sm-6">
			<div class="card">
			<form action="{{ route('user.store') }}" method="post" enctype="multipart/form-data">
				@csrf
				<div class="card-header">
					<h2 class="h6 font-weight-bold mb-0 card-title">Tambah Pengguna</h2>
				</div>
				<div class="card-body">
					<div class="form-group">
						<label>Nama</label>
						<input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" placeholder="Nama" value="{{ old('nama') }}" autofocus>

						@error('nama')
							<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>
					<div class="form-group">
						<label>Username</label>
						<input type="text" class="form-control @error('username') is-invalid @enderror" name="username" placeholder="Username" value="{{ old('username') }}">

						@error('username')
							<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>
					<div class="form-group">
						<label>Hak Akses</label>
						<select class="form-control custom-select @error('hakAkses') is-invalid @enderror" name="hakAkses">
							<option value="1">Super Admin</option>
							<option value="2">Admin</option>
							<option value="3">Gudang</option>
						</select>

						@error('hakAkses')
							<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>

					<div class="form-group">
						<label>Level Approval Usulan Pembelian</label>
						<select class="form-control custom-select @error('approval_level') is-invalid @enderror" name="approval_level">
							<option value="">-- Tidak Ada (Ka. Unit / Pembuat Usulan) --</option>
							<option value="1">Level 1 - Pemeriksa (Diperiksa Oleh)</option>
							<option value="2">Level 2 - Konfirmator (Dikonfirmasi Oleh)</option>
							<option value="3">Level 3 - Diketahui Oleh</option>
							<option value="4">Level 4 - Disetujui Oleh</option>
						</select>
						<small class="text-muted">Tentukan peran user dalam proses persetujuan usulan pembelian</small>

						@error('approval_level')
							<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>
					<div class="form-group">
						<label>Jabatan</label>
						<input type="text" class="form-control @error('jabatan') is-invalid @enderror" name="jabatan" placeholder="cth: Kabag Umum, Kabid Pelayanan, Direktur..." value="{{ old('jabatan') }}">
						<small class="text-muted">Akan ditampilkan di dokumen PDF usulan pembelian</small>

						@error('jabatan')
							<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>




					<div class="form-group">
						<label>Telepon</label>
						<input type="number" class="form-control @error('telepon') is-invalid @enderror" name="telepon" placeholder="Telepon" value="{{ old('telepon') }}" autofocus>

						@error('telepon')
							<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>
					<div class="form-group">
						<label>Alamat</label>
						<textarea class="form-control @error('alamat') is-invalid @enderror" name="alamat" placeholder="Alamat" value="{{ old('alamat') }}"></textarea>

						@error('alamat')
							<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>
					<div class="form-group">
						<label>Ruangan</label>
						<select class="form-control custom-select @error('ruangan_id') is-invalid @enderror" name="ruangan_id">
							<option value="">Pilih Ruangan</option>
							@foreach($ruangans as $ruangan)
								<option value="{{ $ruangan->id }}">{{ $ruangan->kode_ruangan }} - {{ $ruangan->nama_ruangan }}</option>
							@endforeach
						</select>

						@error('ruangan_id')
							<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>
				</div>
				<div class="card-footer">
					<button class="btn btn-primary" type="submit">Tambah</button>
					<a class="btn btn-secondary" href="{{ route('user.index') }}">Kembali</a>
				</div>
			</form>
			</div>
		</div>
	</div>

@endsection