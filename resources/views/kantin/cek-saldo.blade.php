@extends('layouts.app')

@section('title', 'Cek Saldo E-Kantin')

@section('styles')
<style>
    .scan-card {
        border: 2px dashed #17a2b8;
        border-radius: 15px;
        background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
        transition: all 0.3s ease;
    }
    
    .scan-card:hover {
        border-color: #0dcaf0;
        box-shadow: 0 5px 15px rgba(13, 202, 240, 0.3);
    }
    
    .scan-input:focus {
        border: 2px solid #0dcaf0;
        box-shadow: 0 0 15px rgba(13, 202, 240, 0.5);
    }
    
    .saldo-card {
        border: 3px solid #28a745;
        border-radius: 15px;
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        animation: slideIn 0.4s ease-out;
        box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
    }
    
    .saldo-amount {
        font-size: 3.5rem;
        font-weight: 900;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        animation: scaleIn 0.5s ease-out;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes scaleIn {
        from {
            transform: scale(0.5);
        }
        to {
            transform: scale(1);
        }
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }
    
    .alert-shake {
        animation: shake 0.5s ease-out;
    }
    
    .loading-pulse {
        animation: pulse 1s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    /* Badge dengan warna dinamis */
    #limit_badge.bg-info {
        background-color: #0dcaf0 !important;
        color: #fff !important;
        animation: pulseInfo 2s ease-in-out infinite;
    }

    #limit_badge.bg-danger {
        background-color: #dc3545 !important;
        color: #fff !important;
        animation: pulseDanger 2s ease-in-out infinite;
    }

    @keyframes pulseInfo {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(13, 202, 240, 0.7);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(13, 202, 240, 0);
        }
    }

    @keyframes pulseDanger {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
        }
    }
    
    .transaksi-item {
        border-left: 4px solid;
        transition: all 0.3s ease;
    }
    
    .transaksi-item:hover {
        transform: translateX(5px);
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .transaksi-topup {
        border-color: #28a745;
        background: linear-gradient(90deg, #d4edda 0%, #ffffff 100%);
    }
    
    .transaksi-bayar {
        border-color: #dc3545;
        background: linear-gradient(90deg, #f8d7da 0%, #ffffff 100%);
    }
    
    .info-box {
        border-radius: 10px;
        padding: 15px;
        background: #fff;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .info-box:hover {
        border-color: #0dcaf0;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    
    .btn-action {
        transition: all 0.3s ease;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">
        <i class="fas fa-wallet me-2"></i>Cek Saldo E-Kantin
    </h2>
    
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <!-- CARD SCAN RFID -->
            <div class="card shadow-sm mb-4 scan-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>Scan Kartu RFID
                    </h5>
                </div>
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <i class="fas fa-hand-pointer fa-3x text-info mb-3"></i>
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            Tap kartu RFID pada reader untuk cek saldo
                        </p>
                    </div>
                    
                    <input type="text" 
                           id="rfid_card" 
                           class="form-control form-control-lg scan-input text-center" 
                           placeholder="Menunggu kartu RFID..." 
                           autocomplete="off"
                           autofocus>
                </div>
            </div>
            
            <!-- LOADING INDICATOR -->
            <div id="loading" style="display: none;">
                <div class="alert alert-info border-0 shadow-sm loading-pulse text-center">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                    <h5 class="mb-0">Memuat data...</h5>
                </div>
            </div>
            
            <!-- RESULT CONTAINER -->
            <div id="result"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let autoHideTimer = null;

    // ============================================
    // 🎵 SOUND EFFECTS
    // ============================================
    function playSound(type) {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            if (type === 'success') {
                oscillator.frequency.value = 800;
                gainNode.gain.value = 0.3;
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.1);
            } else if (type === 'error') {
                oscillator.frequency.value = 200;
                gainNode.gain.value = 0.3;
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.3);
            }
        } catch(e) {
            console.log('Audio tidak didukung');
        }
    }

    // ============================================
    // 🔍 SCAN RFID
    // ============================================
    $('#rfid_card').on('keypress', function(e) {
        if(e.which === 13) {
            e.preventDefault();
            let rfid = $(this).val().trim();
            
            if(rfid) {
                cekSaldo(rfid);
                $(this).val('');
            } else {
                showError('Silakan scan kartu RFID terlebih dahulu!');
            }
        }
    });

function cekSaldo(rfid) {
    console.log('🔍 Cek saldo RFID:', rfid);
    
    // Clear timer sebelumnya
    if (autoHideTimer) {
        clearTimeout(autoHideTimer);
    }
    
    // Show loading
    $('#loading').slideDown();
    $('#result').slideUp();
    
    $.ajax({
        url: '{{ route("kantin.scan-saldo") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            rfid_card: rfid
        },
        success: function(response) {
            console.log('✅ Data ditemukan:', response);
            
            $('#loading').slideUp();
            playSound('success');
            
            // Ambil data
            let user = response.data.user;
            let saldo = response.data.saldo;
            let limitAktif = response.data.limit_aktif;
            let limitHarian = response.data.limit_harian;
            
            // Badge limit dengan warna dinamis
            let limitBadge = '';
            if (limitAktif == 1 || limitAktif === true) {
                limitBadge = `<span class="badge bg-info fs-6" id="limit_badge">
                    <i class="fas fa-shield-alt me-1"></i>Limit Aktif: Rp ${formatRupiah(limitHarian)}
                </span>`;
            } else {
                limitBadge = `<span class="badge bg-danger fs-6" id="limit_badge">
                    <i class="fas fa-times-circle me-1"></i>Limit Tidak Aktif
                </span>`;
            }
            
            // ✅ Ambil riwayat transaksi dari backend
            getRiwayatTransaksi(user.id, function(transaksiHtml) {
                // Tampilkan hasil
                $('#result').html(`
                    <!-- CARD SALDO UTAMA -->
                    <div class="card saldo-card shadow-lg mb-4">
                        <div class="card-body text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-user-circle fa-4x text-success"></i>
                            </div>
                            
                            <h3 class="text-success mb-3">
                                <i class="fas fa-check-circle me-2"></i>
                                ${user.name}
                            </h3>
                            
                            <div class="mb-4">
                                <small class="text-muted d-block mb-1">Saldo Saat Ini</small>
                                <h1 class="saldo-amount text-success mb-0">
                                    Rp ${formatRupiah(saldo)}
                                </h1>
                            </div>
                            
                            <div class="mb-3">
                                ${limitBadge}
                            </div>
                            
                            <hr class="my-3">
                            
                            <div class="row text-start">
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-id-card me-1"></i>RFID Card
                                    </small>
                                    <strong>${user.rfid_card}</strong>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-envelope me-1"></i>Email
                                    </small>
                                    <strong>${user.email}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- RIWAYAT TRANSAKSI -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">
                                <i class="fas fa-history me-2"></i>
                                5 Transaksi Terakhir
                            </h6>
                        </div>
                        <div class="card-body">
                            ${transaksiHtml}
                        </div>
                    </div>
                    
                    <!-- BUTTON ACTIONS -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center mb-3">
                        <button class="btn btn-primary btn-action" onclick="printSaldo()">
                            <i class="fas fa-print me-2"></i>Print Info Saldo
                        </button>
                        <button class="btn btn-success btn-action" onclick="scanLagi()">
                            <i class="fas fa-redo me-2"></i>Scan Kartu Lagi
                        </button>
                    </div>
                `).slideDown();
                
                // Auto-hide setelah 30 detik
                autoHideTimer = setTimeout(() => {
                    scanLagi();
                }, 30000);
            });
        },
        error: function(xhr) {
            console.log('❌ Error:', xhr.status, xhr.responseJSON);
            
            $('#loading').slideUp();
            playSound('error');
            
            let errorMessage = 'Kartu RFID tidak terdaftar!';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            showError(errorMessage, rfid);
            
            // Re-focus
            setTimeout(() => $('#rfid_card').focus(), 100);
        }
    });
}

// ============================================
// 📊 GET RIWAYAT TRANSAKSI (✅ FIXED)
// ============================================
function getRiwayatTransaksi(userId, callback) {
    $.ajax({
        url: '{{ route("kantin.transaksi-user") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            user_id: userId
        },
        success: function(response) {
            console.log('✅ Transaksi loaded:', response);
            
            if (!response.success || response.data.length === 0) {
                callback(`
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p class="mb-0">Belum ada transaksi</p>
                    </div>
                `);
                return;
            }
            
            let transaksiHtml = '';
            
            response.data.forEach(function(t) {
                // ✅ CEK JENIS TRANSAKSI DARI DATABASE
                if (t.jenis === 'top_up' || t.jenis === 'topup') {
                    // TOP UP (HIJAU, PLUS)
                    transaksiHtml += `
                        <div class="transaksi-item transaksi-topup p-3 mb-2 rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-arrow-up text-success me-2"></i>
                                    <strong>Top Up</strong>
                                    ${t.keterangan ? `<br><small class="text-muted">${t.keterangan}</small>` : ''}
                                </div>
                                <div class="text-end">
                                    <strong class="text-success">+ Rp ${formatRupiah(t.jumlah)}</strong><br>
                                    <small class="text-muted" title="${t.waktu}">
                                        <i class="far fa-clock me-1"></i>${t.waktu_relative}
                                    </small>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    // PEMBAYARAN (MERAH, MINUS)
                    transaksiHtml += `
                        <div class="transaksi-item transaksi-bayar p-3 mb-2 rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-arrow-down text-danger me-2"></i>
                                    <strong>Pembayaran</strong>
                                    ${t.keterangan ? `<br><small class="text-muted">${t.keterangan}</small>` : ''}
                                </div>
                                <div class="text-end">
                                    <strong class="text-danger">- Rp ${formatRupiah(t.jumlah)}</strong><br>
                                    <small class="text-muted" title="${t.waktu}">
                                        <i class="far fa-clock me-1"></i>${t.waktu_relative}
                                    </small>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });
            
            callback(transaksiHtml);
        },
        error: function(xhr) {
            console.log('❌ Error load transaksi:', xhr);
            
            callback(`
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Tidak dapat memuat riwayat transaksi
                </div>
            `);
        }
    });
}

    // ============================================
    // ❌ SHOW ERROR
    // ============================================
    function showError(message, rfid = '') {
        $('#result').html(`
            <div class="alert alert-danger border-0 shadow-sm alert-shake">
                <h5 class="alert-heading">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Kartu Tidak Ditemukan!
                </h5>
                ${rfid ? `
                <div class="bg-white text-dark p-2 rounded mb-2">
                    <strong>RFID: <code class="text-danger">${rfid}</code></strong>
                </div>
                ` : ''}
                <hr>
                <p class="mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    ${message}
                </p>
            </div>
        `).slideDown();
        
        // Auto-hide error setelah 5 detik
        setTimeout(() => {
            $('#result').slideUp();
        }, 5000);
    }

    // ============================================
    // 🔄 SCAN LAGI
    // ============================================
    function scanLagi() {
        if (autoHideTimer) {
            clearTimeout(autoHideTimer);
        }
        
        $('#result').slideUp();
        $('#loading').slideUp();
        $('#rfid_card').val('').focus();
        
        console.log('✅ Siap scan kartu berikutnya');
    }

    // ============================================
    // 🖨️ PRINT SALDO
    // ============================================
    function printSaldo() {
        window.print();
    }

    // ============================================
    // 🔧 HELPER FUNCTIONS
    // ============================================
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    // ============================================
    // 🚀 PAGE LOAD
    // ============================================
    $(document).ready(function() {
        console.log('✅ Cek Saldo page loaded');
        $('#rfid_card').focus();
    });

    // Auto re-focus
    setInterval(function() {
        if (!$('input:focus').length && !$('select:focus').length && !$('textarea:focus').length) {
            if ($('#result').is(':hidden')) {
                $('#rfid_card').focus();
            }
        }
    }, 3000);
</script>
@endsection