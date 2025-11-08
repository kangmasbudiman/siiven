@if (session('success'))
<script>
    showAlert('success', 'Berhasil!', '{{ session('success') }}');
</script>
@endif

@if (session('error'))
<script>
    showAlert('error', 'Gagal!', '{{ session('error') }}');
</script>
@endif

@if (session('warning'))
<script>
    showAlert('warning', 'Peringatan!', '{{ session('warning') }}');
</script>
@endif

@if (session('info'))
<script>
    showAlert('info', 'Informasi', '{{ session('info') }}');
</script>
@endif

@if ($errors->any())
<script>
    showAlert('error', 'Terjadi Kesalahan!', '{{ $errors->first() }}');
</script>
@endif