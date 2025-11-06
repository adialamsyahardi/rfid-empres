@extends('layouts.app')

@section('title', 'Presensi Sekolah')

@section('styles')
<style>
    .badge-ontime {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
    }
    .badge-terlambat {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
    }
    .scan-input {
        font-family: 'Courier New', monospace;
        font-size: 1.2rem;
        text-align: center;
        letter-spacing: 2px;
    }
    .info-card {
        border-radius: 15px;
        transition: transform 0.3s ease;
    }
    .info-card:hover {
        transform: translateY(-5px);
    }

    /* 🆕 TAMBAHKAN ANIMASI INI */
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
        20%, 40%, 60%, 80% { transform: translateX(10px); }
    }

    .fa-spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-user-check me-2"></i>Presensi Sekolah</h2>
        <div>
            <span class="badge bg-primary p-2">
                <i class="fas fa-calendar me-1"></i>{{ date('d F Y') }}
            </span>
            <span class="badge bg-success p-2 ms-2">
                <i class="fas fa-clock me-1"></i><span id="jam-live">{{ date('H:i:s') }}</span>
            </span>
        </div>
    </div>
    
    <!-- Info Pengaturan Waktu -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm info-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box me-3" style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 10px;">
                            <i class="fas fa-sign-in-alt fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Jam Masuk Sekolah</h5>
                            <small style="opacity: 0.9;">Waktu presensi masuk</small>
                        </div>
                    </div>
                    @if($pengaturanMasuk)
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span><i class="fas fa-clock me-2"></i>Jam Mulai</span>
                                <strong style="font-size: 1.3rem;">{{ $pengaturanMasuk->jam_mulai }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span><i class="fas fa-clock me-2"></i>Jam Selesai</span>
                                <strong style="font-size: 1.3rem;">{{ $pengaturanMasuk->jam_selesai }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-hourglass-half me-2"></i>Toleransi</span>
                                <strong style="font-size: 1.3rem;">{{ $pengaturanMasuk->toleransi_keterlambatan }} menit</strong>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-light mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Belum diatur!</strong><br>
                            <small>Silakan atur jam masuk di menu Pengaturan Presensi</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm info-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box me-3" style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 10px;">
                            <i class="fas fa-sign-out-alt fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Jam Pulang Sekolah</h5>
                            <small style="opacity: 0.9;">Waktu presensi pulang</small>
                        </div>
                    </div>
                    @if($pengaturanPulang)
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span><i class="fas fa-clock me-2"></i>Jam Mulai</span>
                                <strong style="font-size: 1.3rem;">{{ $pengaturanPulang->jam_mulai }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span><i class="fas fa-clock me-2"></i>Jam Selesai</span>
                                <strong style="font-size: 1.3rem;">{{ $pengaturanPulang->jam_selesai }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-hourglass-half me-2"></i>Toleransi</span>
                                <strong style="font-size: 1.3rem;">{{ $pengaturanPulang->toleransi_keterlambatan }} menit</strong>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-light mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Belum diatur!</strong><br>
                            <small>Silakan atur jam pulang di menu Pengaturan Presensi</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scan Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Scan Masuk</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Tap kartu RFID untuk presensi masuk
                    </div>
                    <input type="text" 
                           id="rfid_masuk" 
                           class="form-control form-control-lg scan-input" 
                           placeholder="Scan kartu RFID..." 
                           autofocus>
                    <div id="result_masuk" class="mt-3"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-sign-out-alt me-2"></i>Scan Keluar</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>Tap kartu RFID untuk presensi keluar
                    </div>
                    <input type="text" 
                           id="rfid_keluar" 
                           class="form-control form-control-lg scan-input" 
                           placeholder="Scan kartu RFID...">
                    <div id="result_keluar" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Presensi Hari Ini -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Presensi Hari Ini</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jam Masuk</th>
                            <th>Status Masuk</th>
                            <th>Jam Keluar</th>
                            <th>Status Keluar</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($presensi as $index => $p)
                        <tr>
                            <td>{{ $presensi->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $p->user->name }}</strong><br>
                                <small class="text-muted">{{ $p->user->rfid_card }}</small>
                            </td>
                            <td>
                                {{ $p->jam_masuk ?? '-' }}
                            </td>
                            <td>
                                @if($p->jam_masuk)
                                    <span class="badge {{ $p->terlambat_masuk ? 'badge-terlambat' : 'badge-ontime' }}">
                                        <i class="fas {{ $p->terlambat_masuk ? 'fa-exclamation-triangle' : 'fa-check-circle' }}"></i>
                                        {{ $p->terlambat_masuk ? 'Terlambat' : 'Tepat Waktu' }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
<td>
    @if($p->jam_keluar)
        @php
            $statusKeluar = 'normal';
            $pengaturanPulang = \App\Models\PengaturanWaktu::getSekolahPulang();
            
            if ($pengaturanPulang) {
                $jamKeluar = \Carbon\Carbon::createFromFormat('H:i:s', $p->jam_keluar);
                $jamMulaiPulang = \Carbon\Carbon::createFromFormat('H:i:s', $pengaturanPulang->jam_mulai);
                
                if ($jamKeluar->lt($jamMulaiPulang)) {
                    $statusKeluar = 'pulang_cepat';
                    $menitCepat = $jamMulaiPulang->diffInMinutes($jamKeluar);
                }
            }
        @endphp
        
        @if($statusKeluar === 'pulang_cepat')
            {{-- 🔴 PULANG CEPAT = MERAH --}}
            <span class="badge" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; padding: 5px 12px; border-radius: 20px;">
                <i class="fas fa-exclamation-triangle"></i> Pulang Cepat
            </span>
            <br><small class="text-danger">
                <i class="fas fa-clock"></i> -{{ $menitCepat }} menit
            </small>
        @else
            {{-- ✅ NORMAL = HIJAU --}}
            <span class="badge badge-ontime">
                <i class="fas fa-check-circle"></i> Tepat Waktu
            </span>
        @endif
    @else
        <span class="text-muted">-</span>
    @endif
</td>
                            <td>
                                <span class="badge bg-{{ $p->keterangan == 'hadir' ? 'success' : ($p->keterangan == 'izin' ? 'info' : ($p->keterangan == 'sakit' ? 'warning' : 'secondary')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $p->keterangan)) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="updateKeterangan({{ $p->id }}, '{{ $p->keterangan }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada presensi hari ini</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $presensi->links() }}
        </div>
    </div>

    <!-- Siswa Belum Presensi -->
    @if(isset($siswaBelumPresensi) && $siswaBelumPresensi->count() > 0)
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">
                <i class="fas fa-user-times me-2"></i>
                Siswa yang Belum Presensi Hari Ini 
                <span class="badge bg-white text-danger">{{ $siswaBelumPresensi->count() }}</span>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>RFID Card</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswaBelumPresensi as $key => $siswa)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td><strong>{{ $siswa->name }}</strong></td>
                            <td><code>{{ $siswa->rfid_card }}</code></td>
                            <td>
                                <span class="badge bg-danger">
                                    <i class="fas fa-times me-1"></i> Belum Presensi
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>

<!-- Modal Update Keterangan -->
<div class="modal fade" id="modalKeterangan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Update Keterangan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="presensi_id">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="keterangan" value="hadir" id="hadir">
                    <label class="form-check-label" for="hadir">
                        <i class="fas fa-check-circle text-success me-2"></i>Hadir
                    </label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="keterangan" value="izin" id="izin">
                    <label class="form-check-label" for="izin">
                        <i class="fas fa-envelope text-info me-2"></i>Izin
                    </label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="keterangan" value="sakit" id="sakit">
                    <label class="form-check-label" for="sakit">
                        <i class="fas fa-heartbeat text-warning me-2"></i>Sakit
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="keterangan" value="tanpa_keterangan" id="tanpa_keterangan">
                    <label class="form-check-label" for="tanpa_keterangan">
                        <i class="fas fa-question-circle text-secondary me-2"></i>Tanpa Keterangan
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-primary" onclick="simpanKeterangan()">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // ✅ TEST: Cek jQuery loaded
    console.log('jQuery Version:', $.fn.jquery);
    console.log('Page Ready!');

    // Setup CSRF Token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Live clock
    setInterval(() => {
        const now = new Date();
        $('#jam-live').text(now.toLocaleTimeString('id-ID'));
    }, 1000);

    // Auto-focus kembali ke input RFID masuk
    function resetFocus() {
        setTimeout(() => {
            $('#rfid_masuk').focus();
            console.log('Focus reset to RFID Masuk');
        }, 100);
    }

    // Refresh tabel presensi tanpa reload halaman
    function refreshTabelPresensi() {
        $.ajax({
            url: '{{ route("presensi.sekolah.index") }}',
            method: 'GET',
            success: function(response) {
                let newTable = $(response).find('.table-responsive').html();
                $('.table-responsive').html(newTable);
                
                let belumPresensi = $(response).find('.card.shadow-sm.mt-4').last();
                if(belumPresensi.length) {
                    $('.card.shadow-sm.mt-4').last().replaceWith(belumPresensi);
                }
            }
        });
    }

    function scanPresensi(rfid, jenis) {
        console.log('🔍 Scanning:', rfid, 'Jenis:', jenis);
        
        let resultDiv = jenis === 'masuk' ? '#result_masuk' : '#result_keluar';
        
        // Show loading
        $(resultDiv).html(`
            <div class="alert alert-info">
                <i class="fas fa-spinner fa-spin me-2"></i>Memproses...
            </div>
        `);

        $.ajax({
            url: '{{ route("presensi.sekolah.scan") }}',
            method: 'POST',
            data: {
                rfid_card: rfid,
                jenis: jenis
            },
            success: function(response) {
                console.log('✅ Response:', response);
                
                if (!response.data || !response.data.user) {
                    $(resultDiv).html(`
                        <div class="alert alert-warning shadow-sm border-0">
                            <h5 class="alert-heading mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>⚠️ Peringatan
                            </h5>
                            <p class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>${response.message || 'Data tidak lengkap'}
                            </p>
                        </div>
                    `);
                    resetFocus();
                    return;
                }

                let userData = response.data.user;
                let presensiData = response.data.presensi || {};
                
                // CEK PULANG CEPAT dari message
                let isPulangCepat = response.message.includes('lebih awal');
                
                // HANYA CEK terlambat MASUK
                let terlambat = presensiData.terlambat_masuk || false;
                let menitTerlambat = presensiData.menit_terlambat_masuk || 0;
                
                // Tentukan warna notifikasi
                let alertClass = 'alert-success';
                let icon = 'fa-check-circle';
                let title = '✅ Berhasil!';
                
                if (isPulangCepat) {
                    alertClass = 'alert-danger';
                    icon = 'fa-exclamation-triangle';
                    title = '🔴 Pulang Terlalu Cepat!';
                } else if (terlambat) {
                    alertClass = 'alert-warning';
                    icon = 'fa-exclamation-triangle';
                    title = '⚠️ Terlambat!';
                }
                
                $(resultDiv).html(`
                    <div class="alert ${alertClass} shadow-sm border-0" style="animation: slideInRight 0.3s ease-out;">
                        <h5 class="alert-heading mb-3">
                            <i class="fas ${icon} me-2"></i>${title}
                        </h5>
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle bg-white p-2 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user fa-2x text-primary"></i>
                            </div>
                            <div>
                                <strong style="font-size: 1.1rem;">${userData.name}</strong><br>
                                <small class="text-muted"><i class="fas fa-id-card me-1"></i>${userData.rfid_card}</small>
                            </div>
                        </div>
                        <hr>
                        <p class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>${response.message}
                            ${menitTerlambat ? '<br><span class="badge bg-danger mt-2"><i class="fas fa-clock me-1"></i>Terlambat ' + menitTerlambat + ' menit</span>' : ''}
                        </p>
                    </div>
                `);

                refreshTabelPresensi();
                resetFocus();
            },
            error: function(xhr) {
                console.log('❌ Error Status:', xhr.status);
                console.log('❌ Error Response:', xhr.responseJSON);
                
                let errorMessage = 'Terjadi kesalahan';
                let errorIcon = 'fa-exclamation-circle';
                let errorTitle = '❌ Error!';
                
                let responseData = xhr.responseJSON || {};

                if (xhr.status === 404) {
                    errorMessage = 'RFID Card tidak terdaftar dalam sistem!';
                    errorIcon = 'fa-user-times';
                    errorTitle = '🚫 Tidak Ditemukan';
                } else if (xhr.status === 400) {
                    errorMessage = responseData.message || 'Anda sudah melakukan presensi!';
                    errorIcon = 'fa-exclamation-triangle';
                    errorTitle = '⚠️ Peringatan';
                    
                    if (responseData.data && responseData.data.user) {
                        let userData = responseData.data.user;
                        $(resultDiv).html(`
                            <div class="alert alert-warning shadow-sm border-0" style="animation: shake 0.5s ease-out;">
                                <h5 class="alert-heading mb-3">
                                    <i class="fas ${errorIcon} me-2"></i>${errorTitle}
                                </h5>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="rounded-circle bg-white p-2 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user fa-2x text-warning"></i>
                                    </div>
                                    <div>
                                        <strong style="font-size: 1.1rem;">${userData.name}</strong><br>
                                        <small class="text-muted"><i class="fas fa-id-card me-1"></i>${userData.rfid_card}</small>
                                    </div>
                                </div>
                                <hr>
                                <p class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>${errorMessage}
                                </p>
                            </div>
                        `);
                        resetFocus();
                        return;
                    }
                } else if (xhr.status === 422) {
                    errorMessage = responseData.message || 'Validasi gagal';
                    errorIcon = 'fa-exclamation-triangle';
                    errorTitle = '⚠️ Validasi Gagal';
                } else if (xhr.status === 500) {
                    errorMessage = responseData.message || 'Terjadi kesalahan server';
                    errorIcon = 'fa-server';
                    errorTitle = '🔥 Server Error';
                } else if (responseData.message) {
                    errorMessage = responseData.message;
                }

                $(resultDiv).html(`
                    <div class="alert alert-danger shadow-sm border-0" style="animation: shake 0.5s ease-out;">
                        <h5 class="alert-heading mb-3">
                            <i class="fas ${errorIcon} me-2"></i>${errorTitle}
                        </h5>
                        <div class="bg-white text-dark p-2 rounded mb-2">
                            <strong>RFID Card: <code class="text-danger">${rfid}</code></strong>
                        </div>
                        <hr>
                        <p class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>${errorMessage}
                        </p>
                    </div>
                `);

                resetFocus();
            }
        });
    }

    // Event handler untuk input RFID Masuk
    $('#rfid_masuk').on('keypress', function(e) {
        console.log('🔍 Keypress detected:', e.which);
        
        if(e.which === 13) {
            e.preventDefault();
            let rfid = $(this).val().trim();
            
            console.log('✅ Enter pressed! RFID:', rfid);
            
            if(rfid) {
                scanPresensi(rfid, 'masuk');
                $(this).val('');
            } else {
                $('#result_masuk').html(`
                    <div class="alert alert-warning border-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perhatian!</strong> Silakan scan kartu RFID terlebih dahulu!
                    </div>
                `);
            }
        }
    });

    // Event handler untuk input RFID Keluar
    $('#rfid_keluar').on('keypress', function(e) {
        console.log('🔍 Keypress Keluar detected:', e.which);
        
        if(e.which === 13) {
            e.preventDefault();
            let rfid = $(this).val().trim();
            
            console.log('✅ Enter pressed! RFID:', rfid);
            
            if(rfid) {
                scanPresensi(rfid, 'keluar');
                $(this).val('');
            } else {
                $('#result_keluar').html(`
                    <div class="alert alert-warning border-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perhatian!</strong> Silakan scan kartu RFID terlebih dahulu!
                    </div>
                `);
            }
        }
    });

    // Modal Keterangan Functions
    function updateKeterangan(id, currentKeterangan) {
        $('#presensi_id').val(id);
        $(`input[name="keterangan"][value="${currentKeterangan}"]`).prop('checked', true);
        $('#modalKeterangan').modal('show');
    }

    function simpanKeterangan() {
        let presensiId = $('#presensi_id').val();
        let keterangan = $('input[name="keterangan"]:checked').val();

        if(!keterangan) {
            alert('⚠️ Pilih keterangan terlebih dahulu!');
            return;
        }

        let saveBtn = $('button[onclick="simpanKeterangan()"]');
        saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...');

        $.ajax({
            url: '{{ route("presensi.sekolah.update") }}',
            method: 'POST',
            data: {
                presensi_id: presensiId,
                keterangan: keterangan
            },
            success: function(response) {
                $('#modalKeterangan').modal('hide');
                showToast('success', 'Berhasil!', 'Keterangan presensi berhasil diperbarui');
                refreshTabelPresensi();
                saveBtn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Simpan');
            },
            error: function(xhr) {
                let errorMsg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                showToast('danger', 'Gagal!', errorMsg);
                saveBtn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Simpan');
            }
        });
    }

    function showToast(type, title, message) {
        let bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
        let icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        
        let toast = $(`
            <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
                <div class="toast show" role="alert">
                    <div class="toast-header ${bgClass} text-white">
                        <i class="fas ${icon} me-2"></i>
                        <strong class="me-auto">${title}</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">${message}</div>
                </div>
            </div>
        `);
        
        $('body').append(toast);
        setTimeout(() => toast.fadeOut('slow', () => toast.remove()), 3000);
    }

    // Auto-focus saat halaman dimuat
    $(document).ready(function() {
        console.log('✅ Document Ready!');
        
        resetFocus();
        
        $('#modalKeterangan').on('hidden.bs.modal', () => resetFocus());
    });
</script>
@endsection