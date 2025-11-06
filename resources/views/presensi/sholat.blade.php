@extends('layouts.app')

@section('title', 'Presensi Sholat')

@section('styles')
<style>
    .waktu-subuh { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .waktu-dzuhur { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .waktu-ashar { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .waktu-maghrib { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .waktu-isya { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }
    .user-sudah-absen { background-color: #d4edda !important; }
    .user-belum-absen { background-color: #f8d7da !important; }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }
    
    .alert-shake {
        animation: shake 0.5s ease-out;
    }
    
    .banner-permanen {
        position: relative;
        z-index: 100;
        margin-bottom: 1rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        background-color: #fff !important;
    }
    
    .alert-batas-presensi {
        border-left: 5px solid #0dcaf0 !important;
        background-color: #cff4fc !important;
        color: #055160 !important;
        margin-top: 1rem !important;
        margin-bottom: 0 !important;
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
        animation: none !important;
        transition: none !important;
    }
    
    .scan-input:focus {
        border: 2px solid #28a745;
        box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
        transition: all 0.3s ease;
    }
    
    @keyframes pulseLoading {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }
    
    .loading-indicator {
        animation: pulseLoading 1s ease-in-out infinite;
    }
    
    .notif-absensi {
        transition: none !important;
        animation: none !important;
    }
    
    .table td {
        vertical-align: middle;
    }

    .user-sudah-absen:hover {
        background-color: #c3e6cb !important;
        cursor: pointer;
    }

    .user-belum-absen:hover {
        background-color: #f1c1c8 !important;
    }

    .detail-row td {
        padding: 0 !important;
    }

    .badge-sm {
        padding: 2px 5px;
        font-size: 9px;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .detail-row {
        animation: slideDown 0.3s ease-out;
    }

    .btn-xs {
        padding: 2px 5px !important;
        font-size: 10px !important;
        line-height: 1.2 !important;
    }

    .btn-xs i {
        font-size: 9px !important;
    }

    .user-belum-absen:hover .btn-xs {
        background-color: #6c757d !important;
        color: white !important;
        border-color: #6c757d !important;
    }

    .highlight-row {
        background-color: #fff3cd !important;
        border: 2px solid #ffc107 !important;
        box-shadow: 0 0 10px rgba(255, 193, 7, 0.5) !important;
        animation: highlightPulse 1s ease-in-out;
    }

    @keyframes highlightPulse {
        0%, 100% { 
            background-color: #fff3cd;
            transform: scale(1);
        }
        50% { 
            background-color: #ffe69c;
            transform: scale(1.02);
        }
    }

    .hidden-row {
        display: none !important;
    }

    #searchUser:focus {
        border: 2px solid #0d6efd;
        box-shadow: 0 0 10px rgba(13, 110, 253, 0.3);
    }

    .search-no-result {
        text-align: center;
        padding: 2rem;
        color: #6c757d;
    }

    html {
        scroll-behavior: smooth;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Presensi Sholat</h2>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 banner-permanen">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Jadwal Sholat Hari Ini</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3 pb-3 border-bottom">
                        <h3 class="mb-1 text-success fw-bold">{{ $hari ?? 'N/A' }}</h3>
                        <h5 class="text-muted">{{ $tanggal ?? 'N/A' }}</h5>
                    </div>
                    
                    @if($jadwal)
                    <div class="row text-center">
                        <div class="col">
                            <h6>Subuh</h6>
                            <h4>{{ $jadwal->subuh }}</h4>
                        </div>
                        <div class="col">
                            <h6>Dzuhur</h6>
                            <h4>{{ $jadwal->dzuhur }}</h4>
                        </div>
                        <div class="col">
                            <h6>Ashar</h6>
                            <h4>{{ $jadwal->ashar }}</h4>
                        </div>
                        <div class="col">
                            <h6>Maghrib</h6>
                            <h4>{{ $jadwal->maghrib }}</h4>
                        </div>
                        <div class="col">
                            <h6>Isya</h6>
                            <h4>{{ $jadwal->isya }}</h4>
                        </div>
                    </div>
                    
                    <div class="alert alert-info alert-batas-presensi">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Batas Presensi:</strong> {{ $toleransi ? $toleransi->toleransi_keterlambatan : 20 }} menit setelah adzan
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Jadwal sholat belum tersedia untuk hari ini. Silakan hubungi admin untuk sinkronisasi.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach(['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'] as $waktu)
        @php
            $sudahAbsen = $presensi->where('waktu_sholat', $waktu)->pluck('user_id')->toArray();
            $colorClass = 'waktu-' . $waktu;
        @endphp
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-header {{ $colorClass }} text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-mosque me-2"></i>
                        Presensi {{ ucfirst($waktu) }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2" id="stats_{{ $waktu }}">
                        <small class="text-muted">
                            <i class="fas fa-check-circle text-success"></i> Sudah: <strong>{{ count($sudahAbsen) }}</strong> | 
                            <i class="fas fa-times-circle text-danger"></i> Belum: <strong>{{ \App\Models\User::where('role', 'user')->count() - count($sudahAbsen) }}</strong>
                        </small>
                    </div>
                    <input type="text" 
                           class="form-control scan-input" 
                           data-waktu="{{ $waktu }}" 
                           placeholder="Scan kartu RFID..."
                           autocomplete="off">
                    
                    <div id="result_{{ $waktu }}" class="mt-2"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card mt-4 shadow-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" 
                               id="searchUser" 
                               class="form-control" 
                               placeholder="Cari nama atau RFID user..."
                               autocomplete="off">
                        <button class="btn btn-outline-secondary" 
                                type="button" 
                                id="clearSearch"
                                title="Clear">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Ketik nama atau RFID, hasil akan muncul otomatis (Ctrl+F)
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <span class="badge bg-info" id="searchResult">
                        Total User: <strong>{{ \App\Models\User::where('role', 'user')->count() }}</strong>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-users me-2"></i>
                Status & Detail Presensi Sholat Hari Ini
            </h5>
            <div>
                <!-- ✅ TOMBOL EXPORT PDF -->
                <a href="{{ route('presensi.sholat.export-pdf') }}" 
                   class="btn btn-danger btn-sm me-2"
                   target="_blank"
                   title="Export ke PDF (Ctrl+P)">
                    <i class="fas fa-file-pdf me-1"></i>
                    Download Laporan Sholat PDF
                </a>
                
                <span class="badge bg-success">
                    <i class="fas fa-check-circle me-1"></i>
                    Hadir: <strong id="total-hadir">{{ $presensi->count() }}</strong>
                </span>
                <span class="badge bg-danger ms-2">
                    <i class="fas fa-times-circle me-1"></i>
                    Belum: <strong id="total-belum">{{ (\App\Models\User::where('role', 'user')->count() * 5) - $presensi->count() }}</strong>
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover" id="tabelPresensi">
                    <thead class="table-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Nama</th>
                            <th class="text-center" width="120">Subuh</th>
                            <th class="text-center" width="120">Dzuhur</th>
                            <th class="text-center" width="120">Ashar</th>
                            <th class="text-center" width="120">Maghrib</th>
                            <th class="text-center" width="120">Isya</th>
                            <th class="text-center" width="80">Total</th>
                            <th class="text-center" width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $allUsers = \App\Models\User::where('role', 'user')->orderBy('name')->get();
                            $presensiGrouped = $presensi->groupBy('user_id');
                        @endphp
                        @foreach($allUsers as $index => $user)
                        @php
                            $userPresensi = $presensiGrouped->get($user->id, collect());
                            $waktuSholat = ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'];
                            $totalHadir = 0;
                        @endphp
                        <tr data-user-id="{{ $user->id }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $user->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $user->rfid_card }}</small>
                            </td>
                            @foreach($waktuSholat as $waktu)
                                @php
                                    $p = $userPresensi->where('waktu_sholat', $waktu)->first();
                                    $hadir = $p != null;
                                    if ($hadir) $totalHadir++;
                                @endphp
                                <td class="text-center {{ $hadir ? 'user-sudah-absen' : 'user-belum-absen' }}" 
                                    data-waktu="{{ $waktu }}"
                                    style="cursor: {{ $hadir ? 'pointer' : 'default' }};"
                                    title="{{ $hadir ? 'Klik untuk edit' : 'Belum absen' }}">
                                    @if($hadir)
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-check-circle text-success fs-5"></i>
                                            <small class="fw-bold">{{ $p->jam_presensi ?? '-' }}</small>
                                            @if($p->terlambat)
                                                <small class="text-danger">(+{{ $p->menit_terlambat }}m)</small>
                                            @endif
                                            <span class="badge badge-sm bg-{{ $p->keterangan == 'hadir' ? 'success' : ($p->keterangan == 'izin' ? 'info' : ($p->keterangan == 'sakit' ? 'warning' : 'secondary')) }} mt-1" style="font-size: 9px;">
                                                {{ ucfirst(str_replace('_', ' ', $p->keterangan)) }}
                                            </span>
                                        </div>
                                    @else
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-times-circle text-danger fs-5"></i>
                                            <br>
                                            <button class="btn btn-xs btn-outline-secondary mt-1" 
                                                    onclick="tambahKeteranganManual({{ $user->id }}, '{{ $waktu }}')"
                                                    title="Tambah Keterangan"
                                                    style="font-size: 10px; padding: 2px 5px;">
                                                <i class="fas fa-plus-circle me-1"></i>Tambah
                                            </button>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                            <td class="text-center">
                                <strong class="fs-5">{{ $totalHadir }}/5</strong>
                                <br>
                                @php
                                    $persentase = ($totalHadir / 5) * 100;
                                    $badgeColor = $persentase >= 80 ? 'success' : ($persentase >= 60 ? 'warning' : 'danger');
                                @endphp
                                <span class="badge bg-{{ $badgeColor }}">{{ number_format($persentase, 0) }}%</span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-info" onclick="toggleDetail({{ $user->id }})" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <tr id="detail-{{ $user->id }}" class="detail-row" style="display: none;">
                            <td colspan="9" class="bg-light">
                                <div class="p-3">
                                    <h6 class="mb-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Detail Presensi: <strong>{{ $user->name }}</strong>
                                    </h6>
                                    <div class="row">
                                        @foreach($waktuSholat as $waktu)
                                            @php
                                                $p = $userPresensi->where('waktu_sholat', $waktu)->first();
                                            @endphp
                                            <div class="col-md-6 mb-3">
                                                <div class="card {{ $p ? 'border-success' : 'border-danger' }}">
                                                    <div class="card-body p-2">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="mb-0">
                                                                    <i class="fas fa-mosque me-1"></i>
                                                                    {{ ucfirst($waktu) }}
                                                                </h6>
                                                                @if($p)
                                                                    <small class="text-muted">
                                                                        Adzan: {{ $jadwal ? $jadwal->{$waktu} : '-' }} | 
                                                                        Presensi: <strong>{{ $p->jam_presensi ?? '-' }}</strong>
                                                                    </small>
                                                                    @if($p->terlambat)
                                                                        <br>
                                                                        <span class="badge bg-warning text-dark">
                                                                            Terlambat +{{ $p->menit_terlambat }} menit
                                                                        </span>
                                                                    @else
                                                                        <br>
                                                                        <span class="badge bg-success">Tepat Waktu</span>
                                                                    @endif
                                                                @else
                                                                    <small class="text-danger">Belum absen</small>
                                                                @endif
                                                            </div>
                                                            @if($p)
                                                            <div>
                                                                <button class="btn btn-sm btn-primary" 
                                                                        onclick="editKeterangan({{ $p->id }}, '{{ $p->keterangan }}')"
                                                                        title="Edit Keterangan">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            </div>
                                                            @else
                                                            <div>
                                                                <button class="btn btn-sm btn-outline-secondary" 
                                                                        onclick="tambahKeteranganManual({{ $user->id }}, '{{ $waktu }}')"
                                                                        title="Tambah Keterangan">
                                                                    <i class="fas fa-plus-circle"></i>
                                                                </button>
                                                            </div>
                                                            @endif
                                                        </div>
                                                        @if($p)
                                                        <div class="mt-2">
                                                            <span class="badge bg-{{ $p->keterangan == 'hadir' ? 'success' : ($p->keterangan == 'izin' ? 'info' : ($p->keterangan == 'sakit' ? 'warning' : 'secondary')) }}">
                                                                {{ ucfirst(str_replace('_', ' ', $p->keterangan)) }}
                                                            </span>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL UPDATE KETERANGAN -->
<div class="modal fade" id="modalKeterangan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Keterangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="presensi_id">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="keterangan" value="hadir" id="hadir">
                    <label class="form-check-label" for="hadir">
                        <i class="fas fa-check-circle text-success me-1"></i>Hadir
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="keterangan" value="izin" id="izin">
                    <label class="form-check-label" for="izin">
                        <i class="fas fa-info-circle text-info me-1"></i>Izin
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="keterangan" value="sakit" id="sakit">
                    <label class="form-check-label" for="sakit">
                        <i class="fas fa-notes-medical text-warning me-1"></i>Sakit
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="keterangan" value="tanpa_keterangan" id="tanpa_keterangan">
                    <label class="form-check-label" for="tanpa_keterangan">
                        <i class="fas fa-times-circle text-secondary me-1"></i>Tanpa Keterangan
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="simpanKeterangan()">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH KETERANGAN MANUAL -->
<div class="modal fade" id="modalTambahKeterangan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Keterangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="manual_user_id">
                <input type="hidden" id="manual_waktu_sholat">
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    User: <strong id="manual_user_name"></strong><br>
                    Waktu Sholat: <strong id="manual_waktu_display"></strong>
                </div>
                
                <label class="form-label fw-bold">Pilih Keterangan:</label>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="keterangan_manual" value="izin" id="izin_manual">
                    <label class="form-check-label" for="izin_manual">
                        <i class="fas fa-info-circle text-info me-1"></i>Izin
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="keterangan_manual" value="sakit" id="sakit_manual">
                    <label class="form-check-label" for="sakit_manual">
                        <i class="fas fa-notes-medical text-warning me-1"></i>Sakit
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="keterangan_manual" value="tanpa_keterangan" id="tanpa_keterangan_manual">
                    <label class="form-check-label" for="tanpa_keterangan_manual">
                        <i class="fas fa-times-circle text-secondary me-1"></i>Tanpa Keterangan (Alpa)
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="simpanKeteranganManual()">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentFocusedInput = null;
let userIsInteracting = false; // ✅ DETEKSI INTERAKSI USER

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
        } else if (type === 'warning') {
            oscillator.frequency.value = 500;
            gainNode.gain.value = 0.3;
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.2);
        }
    } catch(e) {
        console.log('Audio tidak didukung di browser ini');
    }
}

$(document).ready(function() {
    console.log('✅ Page Loaded!');
    $('.scan-input').first().focus();
    currentFocusedInput = $('.scan-input').first();
});

// ✅ DETEKSI INTERAKSI USER (SCROLL, CLICK, KETIK)
$(document).on('scroll click keydown', function() {
    userIsInteracting = true;
    clearTimeout(window.interactionTimeout);
    
    window.interactionTimeout = setTimeout(function() {
        userIsInteracting = false;
    }, 2000); // 2 detik setelah interaksi terakhir
});

$('.scan-input').on('focus', function() {
    currentFocusedInput = $(this);
});

$('.scan-input').on('keypress', function(e) {
    if(e.which === 13) {
        e.preventDefault();
        
        let rfid = $(this).val().trim();
        let waktu = $(this).data('waktu');
        let inputElement = $(this);
        
        if(rfid) {
            scanPresensi(rfid, waktu, inputElement);
        } else {
            $(`#result_${waktu}`).html(`
                <div class="alert alert-warning alert-shake border-0 shadow-sm">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Perhatian!</strong> Silakan scan kartu RFID terlebih dahulu!
                </div>
            `);
        }
    }
});

// ✅ KEYBOARD SHORTCUTS
$(document).on('keydown', function(e) {
    if ($('.modal').hasClass('show')) return;
    
    // Ctrl+F = Search
    if (e.ctrlKey && e.which === 70) {
        e.preventDefault();
        $('#searchUser').focus().select();
        return;
    }
    
    // Ctrl+P = Export PDF
    if (e.ctrlKey && e.which === 80) {
        e.preventDefault();
        window.open('{{ route("presensi.sholat.export-pdf") }}', '_blank');
        return;
    }
    
    // ESC = Clear Search
    if (e.key === 'Escape') {
        $('#clearSearch').click();
        return;
    }
    
    // Alt+1 s/d Alt+5 = Focus ke waktu sholat
    if (e.altKey && e.which === 49) {
        e.preventDefault();
        $('.scan-input[data-waktu="subuh"]').focus();
    } else if (e.altKey && e.which === 50) {
        e.preventDefault();
        $('.scan-input[data-waktu="dzuhur"]').focus();
    } else if (e.altKey && e.which === 51) {
        e.preventDefault();
        $('.scan-input[data-waktu="ashar"]').focus();
    } else if (e.altKey && e.which === 52) {
        e.preventDefault();
        $('.scan-input[data-waktu="maghrib"]').focus();
    } else if (e.altKey && e.which === 53) {
        e.preventDefault();
        $('.scan-input[data-waktu="isya"]').focus();
    }
});

function scanPresensi(rfid, waktu, inputElement) {
    console.log('🔍 Scanning:', rfid, 'Waktu:', waktu);
    
    $.ajax({
        url: '{{ route("presensi.sholat.scan") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            rfid_card: rfid,
            waktu_sholat: waktu
        },
        beforeSend: function() {
            $(`#result_${waktu}`).html(`
                <div class="alert alert-info border-0 shadow-sm loading-indicator">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    <strong>Memproses...</strong>
                </div>
            `);
        },
        success: function(response) {
            console.log('✅ Success Response:', response);
            
            let keterlambatan = response.data.keterlambatan || {};
            let status = keterlambatan.status || 'tepat_waktu';
            let alertType = response.alert_type || 'success';
            
            let alertClass, iconClass, soundType, title;
            
            if (alertType === 'danger') {
                alertClass = 'alert-danger';
                iconClass = 'fa-exclamation-triangle';
                soundType = 'error';
                title = '⚠️ Terlambat!';
            } else if (alertType === 'info') {
                alertClass = 'alert-info';
                iconClass = 'fa-info-circle';
                soundType = 'success';
                title = '⏰ Terlalu Awal!';
            } else {
                alertClass = 'alert-success';
                iconClass = 'fa-check-circle';
                soundType = 'success';
                title = '✅ Tepat Waktu!';
            }
            
            let notifHtml = `
                <div class="alert ${alertClass} border-0 shadow-sm notif-absensi">
                    <h6 class="alert-heading mb-2">
                        <i class="fas ${iconClass} me-2"></i>${title}
                    </h6>
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle bg-white p-2 me-2" style="width: 40px; height: 40px; display: flex; align-items-center; justify-content: center;">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                        <div>
                            <strong>${response.data.user.name}</strong><br>
                            <small class="text-muted">${response.data.user.rfid_card}</small>
                        </div>
                    </div>
                    <hr class="my-2">
                    <p class="mb-0 small">
                        <i class="fas fa-info-circle me-1"></i>${response.message}
                    </p>
                </div>
            `;
            
            $(`#result_${waktu}`).html(notifHtml);
            playSound(soundType);
            inputElement.val('');
            setTimeout(() => inputElement.focus({ preventScroll: true }), 100);
            updateCounter(waktu);
            updateTable();
        },
        error: function(xhr) {
            console.log('❌ Error:', xhr.status, xhr.responseJSON);
            
            let errorMessage = 'Terjadi kesalahan';
            let userName = '';
            let userRfid = rfid;
            
            if (xhr.responseJSON) {
                errorMessage = xhr.responseJSON.message || errorMessage;
                
                if (xhr.responseJSON.data && xhr.responseJSON.data.user) {
                    userName = xhr.responseJSON.data.user.name;
                    userRfid = xhr.responseJSON.data.user.rfid_card;
                }
            }
            
            let alertClass = 'alert-danger';
            let iconClass = 'fa-exclamation-circle';
            let soundType = 'error';
            let title = '❌ Error!';
            let animClass = 'alert-shake';
            
            if (xhr.status === 400) {
                alertClass = 'alert-warning';
                iconClass = 'fa-info-circle';
                soundType = 'warning';
                title = '⚠️ Peringatan';
            } else if (xhr.status === 404) {
                title = '🚫 Tidak Ditemukan';
                iconClass = 'fa-user-times';
            }
            
            let notifHtml = `
                <div class="alert ${alertClass} border-0 shadow-sm ${animClass}">
                    <h6 class="alert-heading mb-2">
                        <i class="fas ${iconClass} me-2"></i>${title}
                    </h6>
                    ${userName ? `
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle bg-white p-2 me-2" style="width: 40px; height: 40px; display: flex; align-items-center; justify-content: center;">
                            <i class="fas fa-user text-warning"></i>
                        </div>
                        <div>
                            <strong>${userName}</strong><br>
                            <small class="text-muted">${userRfid}</small>
                        </div>
                    </div>
                    <hr class="my-2">
                    ` : `
                    <div class="bg-white text-dark p-2 rounded mb-2">
                        <strong>RFID: <code class="text-danger">${userRfid}</code></strong>
                    </div>
                    `}
                    <p class="mb-0 small">
                        <i class="fas fa-info-circle me-1"></i>${errorMessage}
                    </p>
                </div>
            `;
            
            $(`#result_${waktu}`).html(notifHtml);
            playSound(soundType);
            inputElement.val('');
            setTimeout(() => inputElement.focus({ preventScroll: true }), 100);
        }
    });
}

function updateCounter(waktu) {
    let currentCount = parseInt($(`#stats_${waktu} strong`).first().text()) || 0;
    let currentBelum = parseInt($(`#stats_${waktu} strong`).last().text()) || 0;
    let newCount = currentCount + 1;
    let newBelum = currentBelum - 1;
    
    $(`#stats_${waktu}`).html(`
        <small class="text-muted">
            <i class="fas fa-check-circle text-success"></i> Sudah: <strong>${newCount}</strong> | 
            <i class="fas fa-times-circle text-danger"></i> Belum: <strong>${newBelum}</strong>
        </small>
    `);
}

function updateTable() {
    $.ajax({
        url: '{{ route("presensi.sholat.index") }}',
        method: 'GET',
        success: function(html) {
            let newTableBody = $(html).find('#tabelPresensi tbody').html();
            $('#tabelPresensi tbody').html(newTableBody);
            
            let newTotalHadir = $(html).find('#total-hadir').text();
            let newTotalBelum = $(html).find('#total-belum').text();
            $('#total-hadir').text(newTotalHadir);
            $('#total-belum').text(newTotalBelum);
        }
    });
}

function toggleDetail(userId) {
    userIsInteracting = true; // ✅ TANDAI USER SEDANG INTERAKSI
    
    let detailRow = $(`#detail-${userId}`);
    
    if (detailRow.is(':visible')) {
        detailRow.fadeOut(200);
    } else {
        $('.detail-row').fadeOut(200);
        detailRow.fadeIn(200);
    }
}

function editKeterangan(presensiId, currentKeterangan) {
    userIsInteracting = true; // ✅ TANDAI USER SEDANG INTERAKSI
    
    $('#presensi_id').val(presensiId);
    $('input[name="keterangan"]').prop('checked', false);
    $(`input[name="keterangan"][value="${currentKeterangan}"]`).prop('checked', true);
    $('#modalKeterangan').modal('show');
}

function simpanKeterangan() {
    let presensiId = $('#presensi_id').val();
    let keterangan = $('input[name="keterangan"]:checked').val();

    if(!keterangan) {
        showToast('warning', 'Perhatian!', 'Pilih keterangan terlebih dahulu!');
        return;
    }

    let saveBtn = $('button[onclick="simpanKeterangan()"]');
    saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...');

    $.ajax({
        url: '{{ route("presensi.sholat.update") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            presensi_id: presensiId,
            keterangan: keterangan
        },
        success: function(response) {
            $('#modalKeterangan').modal('hide');
            updateTable();
            showToast('success', 'Berhasil!', 'Keterangan berhasil diubah!');
            saveBtn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Simpan');
        },
        error: function(xhr) {
            showToast('danger', 'Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan');
            saveBtn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Simpan');
        }
    });
}

function tambahKeteranganManual(userId, waktuSholat) {
    userIsInteracting = true; // ✅ TANDAI USER SEDANG INTERAKSI
    
    let userName = $(`tr[data-user-id="${userId}"] td:nth-child(2) strong`).text();
    
    $('#manual_user_id').val(userId);
    $('#manual_waktu_sholat').val(waktuSholat);
    $('#manual_user_name').text(userName);
    $('#manual_waktu_display').text(waktuSholat.charAt(0).toUpperCase() + waktuSholat.slice(1));
    
    $('input[name="keterangan_manual"]').prop('checked', false);
    
    $('#modalTambahKeterangan').modal('show');
}

function simpanKeteranganManual() {
    let userId = $('#manual_user_id').val();
    let waktuSholat = $('#manual_waktu_sholat').val();
    let keterangan = $('input[name="keterangan_manual"]:checked').val();

    if(!keterangan) {
        showToast('warning', 'Perhatian!', 'Pilih keterangan terlebih dahulu!');
        return;
    }

    let saveBtn = $('button[onclick="simpanKeteranganManual()"]');
    saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...');

    $.ajax({
        url: '{{ route("presensi.sholat.store.manual") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            user_id: userId,
            waktu_sholat: waktuSholat,
            keterangan: keterangan
        },
        success: function(response) {
            console.log('✅ Keterangan Manual Saved:', response);
            
            $('#modalTambahKeterangan').modal('hide');
            
            updateCounter(waktuSholat);
            updateTable();
            
            showToast('success', 'Berhasil!', response.message);
            
            saveBtn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Simpan');
        },
        error: function(xhr) {
            console.log('❌ Error:', xhr.responseJSON);
            
            showToast('danger', 'Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan');
            saveBtn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Simpan');
        }
    });
}

// ✅ SEARCH FUNCTION
let searchTimeout;

$('#searchUser').on('input', function() {
    clearTimeout(searchTimeout);
    
    let searchTerm = $(this).val().toLowerCase().trim();
    
    searchTimeout = setTimeout(function() {
        searchUser(searchTerm);
    }, 300);
});

function searchUser(searchTerm) {
    let $tbody = $('#tabelPresensi tbody');
    let $rows = $tbody.find('tr:not(.detail-row)');
    let foundCount = 0;
    let firstMatch = null;
    
    $rows.removeClass('highlight-row');
    $('.detail-row').hide();
    
    if (searchTerm === '') {
        $rows.removeClass('hidden-row');
        $('.detail-row').removeClass('hidden-row');
        $('#searchResult').html(`Total User: <strong>${$rows.length}</strong>`)
            .removeClass('bg-success bg-danger').addClass('bg-info');
        return;
    }
    
    $rows.each(function() {
        let $row = $(this);
        let userId = $row.data('user-id');
        let userName = $row.find('td:nth-child(2) strong').text().toLowerCase();
        let userRfid = $row.find('td:nth-child(2) small').text().toLowerCase();
        
        if (userName.includes(searchTerm) || userRfid.includes(searchTerm)) {
            $row.removeClass('hidden-row');
            $row.addClass('highlight-row');
            $(`#detail-${userId}`).removeClass('hidden-row');
            
            foundCount++;
            
            if (!firstMatch) {
                firstMatch = $row;
            }
        } else {
            $row.addClass('hidden-row');
            $(`#detail-${userId}`).addClass('hidden-row');
        }
    });
    
    if (foundCount > 0) {
        $('#searchResult').html(`
            <i class="fas fa-check-circle me-1"></i>
            Ditemukan: <strong>${foundCount}</strong> user
        `).removeClass('bg-info bg-danger').addClass('bg-success');
        
        if (firstMatch) {
            $('html, body').animate({
                scrollTop: firstMatch.offset().top - 150
            }, 500);
        }
    } else {
        $('#searchResult').html(`
            <i class="fas fa-times-circle me-1"></i>
            Tidak ditemukan
        `).removeClass('bg-info bg-success').addClass('bg-danger');
    }
}

$('#clearSearch').on('click', function() {
    $('#searchUser').val('');
    searchUser('');
    $('#searchUser').focus();
});

// ✅ AUTO-FOCUS YANG TIDAK MENGGANGGU SCROLL (FIXED!)
setInterval(function() {
    // Jangan focus jika:
    // 1. Ada input yang sedang fokus
    // 2. Ada modal terbuka
    // 3. User sedang berinteraksi (scroll/click/ketik)
    // 4. Detail row terbuka
    if ($('input:focus').length || 
        $('select:focus').length || 
        $('textarea:focus').length || 
        $('.modal').hasClass('show') ||
        userIsInteracting ||
        $('.detail-row:visible').length > 0) {
        return;
    }
    
    // Hanya focus jika scroll di posisi atas (dalam 200px dari top)
    if ($(window).scrollTop() > 200) {
        return;
    }
    
    // Focus tanpa scroll
    if (currentFocusedInput) {
        currentFocusedInput.focus({ preventScroll: true });
    }
}, 3000);

// ✅ TOAST NOTIFICATION
function showToast(type, title, message) {
    let bgClass = type === 'success' ? 'bg-success' : (type === 'warning' ? 'bg-warning' : 'bg-danger');
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
</script>
@endsection