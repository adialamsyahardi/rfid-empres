<!-- resources/views/kantin/topup.blade.php -->
@extends('layouts.app')

@section('title', 'Top Up Saldo')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Top Up Saldo E-Kantin</h2>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Form Top Up</h5>
                </div>
                <div class="card-body">
                    <form id="formTopup">
                        <div class="mb-3">
                            <label class="form-label">Pilih User</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">-- Pilih User --</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->rfid_card }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Jumlah Top Up</label>
                            <input type="number" name="jumlah" class="form-control" min="1000" step="1000" required placeholder="Minimal Rp 1.000">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Keterangan (Opsional)</label>
                            <textarea name="keterangan" class="form-control" rows="2"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-check me-2"></i>Proses Top Up
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
    $('#formTopup').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("kantin.proses-topup") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#result').html(`
                    <div class="alert alert-success">
                        <h5>Top Up Berhasil!</h5>
                        <p class="mb-0">Saldo baru: Rp ${formatRupiah(response.saldo_baru)}</p>
                    </div>
                `);
                $('#formTopup')[0].reset();
                setTimeout(() => $('#result').html(''), 5000);
            },
            error: function(xhr) {
                $('#result').html(`
                    <div class="alert alert-danger">
                        ${xhr.responseJSON?.message || 'Terjadi kesalahan'}
                    </div>
                `);
            }
        });
    });

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }
</script>
@endsection
