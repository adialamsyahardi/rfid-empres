@extends('layouts.app')

@section('title', 'Top Up Saldo')

@section('styles')
<style>
    .user-card {
        border: 2px solid #28a745;
        border-radius: 10px;
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
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
    
    .scan-input:focus {
        border: 2px solid #28a745;
        box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
    }
    
    .nominal-btn {
        transition: all 0.3s;
    }
    
    .nominal-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    
    .loading-pulse {
        animation: pulse 1s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    /* ✅ Badge animation dengan warna dinamis */
    .badge {
        transition: all 0.3s ease;
        font-size: 0.9rem;
        padding: 8px 12px;
    }

    /* ✅ Force override Bootstrap badge colors */
    #limit_status.bg-info {
        background-color: #0dcaf0 !important;
        color: #fff !important;
        animation: pulseInfo 2s ease-in-out infinite;
    }

    #limit_status.bg-danger {
        background-color: #dc3545 !important;
        color: #fff !important;
        animation: pulseDanger 2s ease-in-out infinite;
    }

    #limit_status.bg-secondary {
        background-color: #6c757d !important;
        color: #fff !important;
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
</style>
@endsection

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">
        <i class="fas fa-money-bill-wave me-2"></i>Top Up Saldo E-Kantin
    </h2>
    
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <!-- CARD SCAN RFID -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>Scan Kartu RFID
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-barcode me-2"></i>Tap Kartu RFID di Sini
                        </label>
                        <input type="text" 
                               id="rfid_scan" 
                               class="form-control form-control-lg scan-input" 
                               placeholder="Letakkan kartu pada reader..."
                               autocomplete="off"
                               autofocus>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Kartu akan terdeteksi otomatis saat di-tap
                        </small>
                    </div>
                    
                    <!-- NOTIFIKASI SCAN -->
                    <div id="scan_result"></div>
                </div>
            </div>
            
            <!-- CARD INFO USER (Hidden dulu) -->
            <div id="user_info" style="display: none;">
                <div class="card shadow-sm mb-4 user-card">
                    <div class="card-body">
                        <h5 class="card-title text-success mb-3">
                            <i class="fas fa-user-check me-2"></i>User Ditemukan!
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <small class="text-muted d-block">Nama</small>
                                <strong id="user_name" class="fs-5">-</strong>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted d-block">RFID Card</small>
                                <strong id="user_rfid" class="fs-5">-</strong>
                            </div>
                        </div>
                        
                        <hr>
                        
<div class="row">
    <div class="col-md-6 mb-2">
        <small class="text-muted d-block">Saldo Saat Ini</small>
        <h4 class="text-success mb-0">
            <i class="fas fa-wallet me-1"></i>
            Rp <span id="user_saldo">0</span>
        </h4>
    </div>
    <div class="col-md-6 mb-2">
        <small class="text-muted d-block">Limit Harian</small>
        <h6 class="mb-0">
            <span class="badge bg-secondary" id="limit_status">
                <i class="fas fa-hourglass-half me-1"></i>Memuat...
            </span>
        </h6>
    </div>
</div>
                        
                        <button type="button" 
                                class="btn btn-outline-danger btn-sm mt-2" 
                                onclick="resetForm()">
                            <i class="fas fa-times me-1"></i>Ganti User
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- FORM TOPUP (Hidden sampai user dipilih) -->
            <div id="form_topup" style="display: none;">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>Form Top Up Saldo
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="formTopup">
                            <input type="hidden" id="selected_user_id" name="user_id">
                            
                            <!-- Pilihan Nominal Cepat -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Pilihan Nominal Cepat</label>
                                <div class="row g-2">
                                    <div class="col-6 col-md-3">
                                        <button type="button" 
                                                class="btn btn-outline-success w-100 nominal-btn" 
                                                onclick="setNominal(10000)">
                                            Rp 10.000
                                        </button>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <button type="button" 
                                                class="btn btn-outline-success w-100 nominal-btn" 
                                                onclick="setNominal(20000)">
                                            Rp 20.000
                                        </button>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <button type="button" 
                                                class="btn btn-outline-success w-100 nominal-btn" 
                                                onclick="setNominal(50000)">
                                            Rp 50.000
                                        </button>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <button type="button" 
                                                class="btn btn-outline-success w-100 nominal-btn" 
                                                onclick="setNominal(100000)">
                                            Rp 100.000
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Input Nominal Manual -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Atau Masukkan Nominal Manual</label>
                                <input type="number" 
                                       id="jumlah" 
                                       name="jumlah" 
                                       class="form-control form-control-lg" 
                                       min="1000" 
                                       step="1000" 
                                       required 
                                       placeholder="Minimal Rp 1.000">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Minimal topup Rp 1.000
                                </small>
                            </div>
                            
                            <!-- Keterangan -->
                            <div class="mb-3">
                                <label class="form-label">Keterangan (Opsional)</label>
                                <textarea name="keterangan" 
                                          class="form-control" 
                                          rows="2" 
                                          placeholder="Contoh: Top up untuk uang jajan"></textarea>
                            </div>
                            
                            <!-- Button Submit -->
                            <button type="submit" 
                                    class="btn btn-success btn-lg w-100">
                                <i class="fas fa-check-circle me-2"></i>
                                Proses Top Up Sekarang
                            </button>
                        </form>
                        
                        <!-- Result Topup -->
                        <div id="topup_result" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentUser = null;

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
    $('#rfid_scan').on('keypress', function(e) {
        if(e.which === 13) {
            e.preventDefault();
            let rfid = $(this).val().trim();
            
            if(rfid) {
                cariUser(rfid);
                $(this).val('');
            } else {
                showScanError('Silakan scan kartu RFID terlebih dahulu!');
            }
        }
    });

function cariUser(rfid) {
    console.log('🔍 Mencari user dengan RFID:', rfid);
    
    // Show loading
    $('#scan_result').html(`
        <div class="alert alert-info border-0 shadow-sm loading-pulse">
            <i class="fas fa-spinner fa-spin me-2"></i>
            <strong>Mencari data user...</strong>
        </div>
    `);

    $.ajax({
        url: '{{ route("kantin.cari-user-topup") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            rfid_card: rfid
        },
        success: function(response) {
            console.log('✅ User ditemukan:', response);
            
            playSound('success');
            
            currentUser = response.data;
            
            // Tampilkan notifikasi sukses
            $('#scan_result').html(`
                <div class="alert alert-success border-0 shadow-sm">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>User ditemukan!</strong> Silakan isi nominal top up.
                </div>
            `);
            
            // Tampilkan info user
            $('#user_name').text(response.data.name);
            $('#user_rfid').text(response.data.rfid_card);
            $('#user_saldo').text(formatRupiah(response.data.saldo));
            
            // ✅ UPDATE LIMIT STATUS DENGAN WARNA DINAMIS
            console.log('Limit aktif:', response.data.limit_aktif); // Debug
            
            if (response.data.limit_aktif == 1 || response.data.limit_aktif === true) {
                // LIMIT AKTIF → BIRU
                $('#limit_status')
                    .removeClass('bg-secondary bg-danger bg-warning bg-success')
                    .addClass('bg-info')
                    .html('<i class="fas fa-shield-alt me-1"></i>Aktif - Rp ' + formatRupiah(response.data.limit_harian));
                
                console.log('✅ Badge set to BLUE (Aktif)');
            } else {
                // LIMIT TIDAK AKTIF → MERAH
                $('#limit_status')
                    .removeClass('bg-secondary bg-info bg-warning bg-success')
                    .addClass('bg-danger')
                    .html('<i class="fas fa-times-circle me-1"></i>Tidak Aktif');
                
                console.log('✅ Badge set to RED (Tidak Aktif)');
            }
            
            $('#selected_user_id').val(response.data.id);
            
            // Show user info & form
            $('#user_info').slideDown();
            $('#form_topup').slideDown();
            
            // Focus ke input nominal
            setTimeout(() => $('#jumlah').focus(), 300);
            
            // Auto-hide scan result setelah 3 detik
            setTimeout(() => {
                $('#scan_result').fadeOut('slow', function() {
                    $(this).html('').show();
                });
            }, 3000);
        },
        error: function(xhr) {
            console.log('❌ Error:', xhr.status, xhr.responseJSON);
            
            playSound('error');
            
            let errorMessage = 'Terjadi kesalahan';
            
            if (xhr.status === 404) {
                errorMessage = 'RFID tidak terdaftar dalam sistem!';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            showScanError(errorMessage, rfid);
            
            // Re-focus ke input scan
            setTimeout(() => $('#rfid_scan').focus(), 100);
        }
    });
}

    function showScanError(message, rfid = '') {
        $('#scan_result').html(`
            <div class="alert alert-danger border-0 shadow-sm alert-shake">
                <h6 class="alert-heading">
                    <i class="fas fa-exclamation-circle me-2"></i>Error!
                </h6>
                ${rfid ? '<div class="bg-white text-dark p-2 rounded mb-2"><strong>RFID: <code class="text-danger">' + rfid + '</code></strong></div>' : ''}
                <p class="mb-0">
                    <i class="fas fa-info-circle me-1"></i>${message}
                </p>
            </div>
        `);
    }

    // ============================================
    // 💰 SET NOMINAL CEPAT
    // ============================================
    function setNominal(nominal) {
        $('#jumlah').val(nominal);
        $('#jumlah').focus();
    }

    // ============================================
    // 🔄 RESET FORM
    // ============================================
    function resetForm() {
        currentUser = null;
        $('#user_info').slideUp();
        $('#form_topup').slideUp();
        $('#formTopup')[0].reset();
        $('#scan_result').html('');
        $('#topup_result').html('');
        $('#rfid_scan').val('').focus();
    }

    // ============================================
    // 💸 PROSES TOPUP
    // ============================================
    $('#formTopup').on('submit', function(e) {
        e.preventDefault();
        
        let jumlah = parseInt($('#jumlah').val());
        
        if (jumlah < 1000) {
            $('#topup_result').html(`
                <div class="alert alert-warning border-0 shadow-sm">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Minimal top up adalah Rp 1.000!
                </div>
            `);
            return;
        }
        
        // Konfirmasi
        if (!confirm(`Konfirmasi top up Rp ${formatRupiah(jumlah)} untuk ${currentUser.name}?`)) {
            return;
        }
        
        let submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Memproses...');
        
        $.ajax({
            url: '{{ route("kantin.proses-topup") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                console.log('✅ Topup berhasil:', response);
                
                playSound('success');
                
                $('#topup_result').html(`
                    <div class="alert alert-success border-0 shadow-sm">
                        <h5 class="alert-heading">
                            <i class="fas fa-check-circle me-2"></i>
                            Top Up Berhasil!
                        </h5>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted d-block">Nama</small>
                                <strong>${response.data.nama}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Jumlah Top Up</small>
                                <strong class="text-success">Rp ${formatRupiah(response.data.jumlah_topup)}</strong>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted d-block">Saldo Sebelum</small>
                                <strong>Rp ${formatRupiah(response.data.saldo_sebelum)}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Saldo Sekarang</small>
                                <strong class="text-success fs-5">Rp ${formatRupiah(response.data.saldo_baru)}</strong>
                            </div>
                        </div>
                    </div>
                `);
                
                // Update saldo di card user
                $('#user_saldo').text(formatRupiah(response.data.saldo_baru));
                
                // Reset form setelah 5 detik
                setTimeout(() => {
                    resetForm();
                }, 5000);
                
                submitBtn.prop('disabled', false).html('<i class="fas fa-check-circle me-2"></i>Proses Top Up Sekarang');
            },
            error: function(xhr) {
                console.log('❌ Error:', xhr.responseJSON);
                
                playSound('error');
                
                $('#topup_result').html(`
                    <div class="alert alert-danger border-0 shadow-sm">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        ${xhr.responseJSON?.message || 'Terjadi kesalahan saat memproses top up!'}
                    </div>
                `);
                
                submitBtn.prop('disabled', false).html('<i class="fas fa-check-circle me-2"></i>Proses Top Up Sekarang');
            }
        });
    });

    // ============================================
    // 🔧 HELPER FUNCTIONS
    // ============================================
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    // Auto-focus ke scan input saat load
    $(document).ready(function() {
        console.log('✅ Topup page loaded');
        $('#rfid_scan').focus();
    });

    // Re-focus ke scan input jika tidak ada yang difokuskan
    setInterval(function() {
        if (!$('input:focus').length && !$('select:focus').length && !$('textarea:focus').length) {
            if (!$('#user_info').is(':visible')) {
                $('#rfid_scan').focus();
            }
        }
    }, 3000);
</script>
@endsection