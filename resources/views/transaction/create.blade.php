@extends('_layouts.app')

@section('title', 'Input Transaksi')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">💰 Input Transaksi</h4>
                </div>
                <div class="card-body">
                    
                    <!-- Quick Action Buttons -->
                    <div class="row mb-4">
                        <div class="col-4">
                            <button class="btn btn-success w-100" onclick="showForm('sale')">
                                🛒 Jual
                            </button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-info w-100" onclick="showForm('reload')">
                                🔄 Top-up
                            </button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-warning w-100" onclick="showForm('payment')">
                                💵 Bayar Hutang
                            </button>
                        </div>
                    </div>

                    <!-- Sale Form -->
                    <form id="saleForm" class="transaction-form">
                        @csrf
                        <h6>🛒 Penjualan</h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Aplikasi</label>
                            <select name="application_id" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                @foreach($applications as $app)
                                <option value="{{ $app->id }}">{{ $app->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tipe Customer</label>
                            <select name="customer_type" class="form-select" onchange="toggleReseller(this.value)">
                                <option value="non_reseller">Non-Reseller</option>
                                <option value="reseller">Reseller</option>
                            </select>
                        </div>

                        <div class="mb-3" id="resellerField" style="display:none">
                            <label class="form-label">Reseller</label>
                            <select name="reseller_id" class="form-select">
                                <option value="">-- Pilih --</option>
                                @foreach($resellers as $reseller)
                                <option value="{{ $reseller->id }}">{{ $reseller->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Coin</label>
                                    <input type="number" name="quantity" class="form-control" min="1" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Total Bayar (Rp)</label>
                                    <input type="number" name="amount" class="form-control" min="0" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Simpan Penjualan</button>
                    </form>

                    <!-- Reload Form -->
                    <form id="reloadForm" class="transaction-form" style="display:none">
                        @csrf
                        <h6>🔄 Top-up Coin</h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Aplikasi</label>
                            <select name="application_id" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                @foreach($applications as $app)
                                <option value="{{ $app->id }}">{{ $app->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah Coin</label>
                            <input type="number" name="quantity" class="form-control" min="1" required>
                        </div>

                        <button type="submit" class="btn btn-info w-100">Simpan Top-up</button>
                    </form>

                    <!-- Payment Form -->
                    <form id="paymentForm" class="transaction-form" style="display:none">
                        @csrf
                        <h6>💵 Bayar Hutang</h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Reseller</label>
                            <select name="reseller_id" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                @foreach($resellers as $reseller)
                                <option value="{{ $reseller->id }}">{{ $reseller->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah Bayar (Rp)</label>
                            <input type="number" name="amount" class="form-control" min="1" required>
                        </div>

                        <button type="submit" class="btn btn-warning w-100">Simpan Pembayaran</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showForm(type) {
    $('.transaction-form').hide();
    $('#' + type + 'Form').show();
}

function toggleReseller(type) {
    if (type === 'reseller') {
        $('#resellerField').show();
    } else {
        $('#resellerField').hide();
    }
}

// Form submissions
$('#saleForm').on('submit', function(e) {
    e.preventDefault();
    submitForm('sale', this);
});

$('#reloadForm').on('submit', function(e) {
    e.preventDefault();
    submitForm('reload', this);
});

$('#paymentForm').on('submit', function(e) {
    e.preventDefault();
    submitForm('payment', this);
});

function submitForm(type, form) {
    const formData = new FormData(form);
    const url = '/transactions/' + type;

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            form.reset();
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
}
</script>
@endpush