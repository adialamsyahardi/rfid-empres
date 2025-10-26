<!-- resources/views/kantin/bayar.blade.php -->
@extends('layouts.app')

@section('title', 'Pembayaran Kantin')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Pembayaran Kantin</h2>
    
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-cash-register me-2"></i>Scan untuk Bayar</h5>
                </div>
                <div class="card-body">
                    <form id="formBayar">
                        <div class="mb-3">
                            <label class="form-label">Scan Kartu RFID</label>
                            <input type="text" name="rfid_card" id="rfid_card" class="form-control form-control-lg" placeholder="Tap kartu RFID..." required autofocus>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Jumlah Pembayaran</label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control form-control-lg" min="100" step="100" required placeholder="Rp">
                        </div>
                        
                        <button type="submit" class="btn btn-danger btn-lg w-100">
                            <i class="fas fa-shopping-cart me-2"></i>Proses Pembayaran
                        </button>
                    </form>
                    
                    <div id="result" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('#formBayar').on('submit', function(e) {
        e.preventDefault();
        
        let jumlah = $('#jumlah').val();
        if(confirm(`Yakin ingin melakukan pembayaran sebesar Rp ${formatRupiah(jumlah)}?`)) {
            $.ajax({
                url: '{{ route("kantin.proses-bayar") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#result').html(`
                        <div class="alert alert-success">
                            <h5>Pembayaran Berhasil!</h5>
                            <p><strong>Nama:</strong> ${response.data.nama}</p>
                            <p><strong>Jumlah Bayar:</strong> Rp ${formatRupiah(response.data.jumlah_bayar)}</p>
                            <p><strong>Saldo Sebelum:</strong> Rp ${formatRupiah(response.data.saldo_sebelum)}</p>
                            <p class="mb-0"><strong>Saldo Sesudah:</strong> Rp ${formatRupiah(response.data.saldo_sesudah)}</p>
                        </div>
                    `);
                    $('#formBayar')[0].reset();
                    $('#rfid_card').focus();
                    setTimeout(() => $('#result').html(''), 5000);
                },
                error: function(xhr) {
                    $('#result').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle me-2"></i>
                            ${xhr.responseJSON?.message || 'Terjadi kesalahan'}
                        </div>
                    `);
                    $('#formBayar')[0].reset();
                    $('#rfid_card').focus();
                }
            });
        }
    });

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }
</script>
@endsection
