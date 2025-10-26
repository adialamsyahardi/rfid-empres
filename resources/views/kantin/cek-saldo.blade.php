<!-- resources/views/kantin/cek-saldo.blade.php -->
@extends('layouts.app')

@section('title', 'Cek Saldo')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Cek Saldo E-Kantin</h2>
    
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-wallet me-2"></i>Scan Kartu untuk Cek Saldo</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Tap kartu RFID pada reader
                    </div>
                    <input type="text" id="rfid_card" class="form-control form-control-lg" placeholder="Scan kartu RFID..." autofocus>
                    
                    <div id="result" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('#rfid_card').on('keypress', function(e) {
        if(e.which === 13) {
            let rfid = $(this).val().trim();
            if(rfid) {
                cekSaldo(rfid);
                $(this).val('');
            }
        }
    });

    function cekSaldo(rfid) {
        $.ajax({
            url: '{{ route("kantin.scan-saldo") }}',
            method: 'POST',
            data: { rfid_card: rfid },
            success: function(response) {
                $('#result').html(`
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3>${response.data.user.name}</h3>
                            <h1 class="display-3">Rp ${formatRupiah(response.data.saldo)}</h1>
                            <p class="mb-0">
                                ${response.data.limit_aktif ? 
                                    `Limit Harian: Rp ${formatRupiah(response.data.limit_harian)}` : 
                                    'Limit tidak aktif'
                                }
                            </p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <p><strong>RFID:</strong> ${response.data.user.rfid_card}</p>
                        <p><strong>Email:</strong> ${response.data.user.email}</p>
                    </div>
                `);
                setTimeout(() => $('#result').html(''), 5000);
            },
            error: function(xhr) {
                $('#result').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle me-2"></i>
                        ${xhr.responseJSON?.message || 'Kartu tidak terdaftar'}
                    </div>
                `);
            }
        });
    }

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }
</script>
@endsection
