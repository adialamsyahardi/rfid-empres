
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
</style>
@endsection

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Presensi Sholat</h2>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Jadwal Sholat Hari Ini</h5>
                </div>
                <div class="card-body">
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
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Toleransi Keterlambatan:</strong> {{ $toleransi ? $toleransi->toleransi_keterlambatan : 20 }} menit setelah adzan
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
            <div class="card">
                <div class="card-header {{ $colorClass }} text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-mosque me-2"></i>
                        Presensi {{ ucfirst($waktu) }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-check-circle text-success"></i> Sudah: {{ count($sudahAbsen) }} | 
                            <i class="fas fa-times-circle text-danger"></i> Belum: {{ \App\Models\User::where('role', 'user')->count() - count($sudahAbsen) }}
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

    <!-- Daftar User dan Status Absensi -->
    <div class="card mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Status Absensi Semua User Hari Ini</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th class="text-center">Subuh</th>
                            <th class="text-center">Dzuhur</th>
                            <th class="text-center">Ashar</th>
                            <th class="text-center">Maghrib</th>
                            <th class="text-center">Isya</th>
                            <th class="text-center">Total</th>
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
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $user->name }}</strong></td>
                            @foreach($waktuSholat as $waktu)
                                @php
                                    $p = $userPresensi->where('waktu_sholat', $waktu)->first();
                                    $hadir = $p != null;
                                    if ($hadir) $totalHadir++;
                                @endphp
                                <td class="text-center {{ $hadir ? 'user-sudah-absen' : 'user-belum-absen' }}">
                                    @if($hadir)
                                        <i class="fas fa-check-circle text-success"></i>
                                        <br><small>{{ $p->jam_presensi }}</small>
                                        @if($p->terlambat)
                                            <br><small class="text-danger">(+{{ $p->menit_terlambat }}m)</small>
                                        @endif
                                    @else
                                        <i class="fas fa-times-circle text-danger"></i>
                                    @endif
                                </td>
                            @endforeach
                            <td class="text-center"><strong>{{ $totalHadir }}/5</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Daftar Presensi Detail -->
    <div class="card mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Detail Presensi Sholat Hari Ini</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Waktu Sholat</th>
                            <th>Jam Adzan</th>
                            <th>Jam Presensi</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($presensi as $index => $p)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $p->user->name }}</td>
                            <td>
                                <span class="badge bg-info">
                                    {{ ucfirst($p->waktu_sholat) }}
                                </span>
                            </td>
                            <td>{{ $jadwal ? $jadwal->{$p->waktu_sholat} : '-' }}</td>
                            <td>
                                {{ $p->jam_presensi }}
                                @if($p->terlambat)
                                    <br><small class="text-danger">(+{{ $p->menit_terlambat }} menit)</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $p->terlambat ? 'badge-terlambat' : 'badge-ontime' }}">
                                    {{ $p->terlambat ? 'Terlambat' : 'Tepat Waktu' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $p->keterangan == 'hadir' ? 'success' : ($p->keterangan == 'izin' ? 'info' : ($p->keterangan == 'sakit' ? 'warning' : 'secondary')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $p->keterangan)) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="updateKeterangan({{ $p->id }})">
                                    <i class="fas fa-edit"></i> Ubah
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada presensi sholat hari ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Keterangan -->
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
                    <label class="form-check-label" for="hadir">Hadir</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="keterangan" value="izin" id="izin">
                    <label class="form-check-label" for="izin">Izin</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="keterangan" value="sakit" id="sakit">
                    <label class="form-check-label" for="sakit">Sakit</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="keterangan" value="tanpa_keterangan" id="tanpa_keterangan">
                    <label class="form-check-label" for="tanpa_keterangan">Tanpa Keterangan</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="simpanKeterangan()">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentFocusedInput = null;

    $(document).ready(function() {
        // Auto focus ke input pertama
        $('.scan-input').first().focus();
        currentFocusedInput = $('.scan-input').first();
    });

    $('.scan-input').on('focus', function() {
        currentFocusedInput = $(this);
    });

    $('.scan-input').on('keypress', function(e) {
        if(e.which === 13) {
            let rfid = $(this).val().trim();
            let waktu = $(this).data('waktu');
            let inputElement = $(this);
            
            if(rfid) {
                scanPresensi(rfid, waktu, inputElement);
            }
        }
    });

    function scanPresensi(rfid, waktu, inputElement) {
        $.ajax({
            url: '{{ route("presensi.sholat.scan") }}',
            method: 'POST',
            data: {
                rfid_card: rfid,
                waktu_sholat: waktu
            },
            success: function(response) {
                let alertClass = response.data.presensi.terlambat ? 'alert-warning' : 'alert-success';
                
                $(`#result_${waktu}`).html(`
                    <div class="alert ${alertClass} alert-sm">
                        <strong>${response.data.user.name}</strong><br>
                        ${response.message}
                    </div>
                `);
                
                // Clear input
                inputElement.val('');
                
                // PENTING: Kembalikan focus ke input yang sama
                setTimeout(() => {
                    inputElement.focus();
                }, 100);
                
                // Reload setelah 2 detik untuk update tabel
                setTimeout(() => {
                    // Simpan waktu sholat yang sedang aktif
                    sessionStorage.setItem('lastActiveWaktu', waktu);
                    location.reload();
                }, 2000);
            },
            error: function(xhr) {
                $(`#result_${waktu}`).html(`
                    <div class="alert alert-danger alert-sm">
                        ${xhr.responseJSON?.message || 'Terjadi kesalahan'}
                    </div>
                `);
                
                // Clear input dan kembalikan focus
                inputElement.val('');
                inputElement.focus();
            }
        });
    }

    // Setelah reload, fokus kembali ke input yang terakhir digunakan
    $(document).ready(function() {
        let lastWaktu = sessionStorage.getItem('lastActiveWaktu');
        if (lastWaktu) {
            let targetInput = $(`.scan-input[data-waktu="${lastWaktu}"]`);
            if (targetInput.length) {
                targetInput.focus();
                currentFocusedInput = targetInput;
            }
            sessionStorage.removeItem('lastActiveWaktu');
        }
    });

    // Auto re-focus jika user klik di tempat lain
    setInterval(function() {
        if (!$('input:focus').length && !$('select:focus').length && !$('textarea:focus').length && !$('.modal').hasClass('show')) {
            if (currentFocusedInput) {
                currentFocusedInput.focus();
            }
        }
    }, 3000);

    function updateKeterangan(id) {
        $('#presensi_id').val(id);
        $('#modalKeterangan').modal('show');
    }

    function simpanKeterangan() {
        let presensiId = $('#presensi_id').val();
        let keterangan = $('input[name="keterangan"]:checked').val();

        if(!keterangan) {
            alert('Pilih keterangan terlebih dahulu!');
            return;
        }

        $.ajax({
            url: '{{ route("presensi.sholat.update") }}',
            method: 'POST',
            data: {
                presensi_id: presensiId,
                keterangan: keterangan
            },
            success: function(response) {
                $('#modalKeterangan').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + xhr.responseJSON?.message);
            }
        });
    }

    // Auto refresh jadwal setiap 60 detik
    setInterval(function() {
        $.get('{{ route("presensi.sholat.jadwal") }}', function(response) {
            if(response.success && response.jadwal) {
                // Update tampilan jadwal jika diperlukan
            }
        });
    }, 60000);
</script>
</div>

<!-- Modal Update Keterangan -->
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
                    <label class="form-check-label" for="hadir">Hadir</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="keterangan" value="izin" id="izin">
                    <label class="form-check-label" for="izin">Izin</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="keterangan" value="sakit" id="sakit">
                    <label class="form-check-label" for="sakit">Sakit</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="keterangan" value="tanpa_keterangan" id="tanpa_keterangan">
                    <label class="form-check-label" for="tanpa_keterangan">Tanpa Keterangan</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="simpanKeterangan()">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('.scan-input').on('keypress', function(e) {
        if(e.which === 13) {
            let rfid = $(this).val().trim();
            let waktu = $(this).data('waktu');
            
            if(rfid) {
                scanPresensi(rfid, waktu);
                $(this).val('');
            }
        }
    });

    function scanPresensi(rfid, waktu) {
        $.ajax({
            url: '{{ route("presensi.sholat.scan") }}',
            method: 'POST',
            data: {
                rfid_card: rfid,
                waktu_sholat: waktu
            },
            success: function(response) {
                let alertClass = response.data.presensi.terlambat ? 'alert-warning' : 'alert-success';
                
                $(`#result_${waktu}`).html(`
                    <div class="alert ${alertClass} alert-sm">
                        <strong>${response.data.user.name}</strong><br>
                        ${response.message}
                    </div>
                `);
                setTimeout(() => location.reload(), 2000);
            },
            error: function(xhr) {
                $(`#result_${waktu}`).html(`
                    <div class="alert alert-danger alert-sm">
                        ${xhr.responseJSON?.message || 'Terjadi kesalahan'}
                    </div>
                `);
            }
        });
    }

    function updateKeterangan(id) {
        $('#presensi_id').val(id);
        $('#modalKeterangan').modal('show');
    }

    function simpanKeterangan() {
        let presensiId = $('#presensi_id').val();
        let keterangan = $('input[name="keterangan"]:checked').val();

        if(!keterangan) {
            alert('Pilih keterangan terlebih dahulu!');
            return;
        }

        $.ajax({
            url: '{{ route("presensi.sholat.update") }}',
            method: 'POST',
            data: {
                presensi_id: presensiId,
                keterangan: keterangan
            },
            success: function(response) {
                $('#modalKeterangan').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + xhr.responseJSON?.message);
            }
        });
    }

    // Auto refresh jadwal setiap 60 detik
    setInterval(function() {
        $.get('{{ route("presensi.sholat.jadwal") }}', function(response) {
            if(response.success && response.jadwal) {
                // Update tampilan jadwal jika diperlukan
            }
        });
    }, 60000);
</script>
</div>

<!-- Modal Update Keterangan -->
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
                    <label class="form-check-label" for="hadir">Hadir</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="keterangan" value="izin" id="izin">
                    <label class="form-check-label" for="izin">Izin</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="keterangan" value="sakit" id="sakit">
                    <label class="form-check-label" for="sakit">Sakit</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="keterangan" value="tanpa_keterangan" id="tanpa_keterangan">
                    <label class="form-check-label" for="tanpa_keterangan">Tanpa Keterangan</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="simpanKeterangan()">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('.scan-input').on('keypress', function(e) {
        if(e.which === 13) {
            let rfid = $(this).val().trim();
            let waktu = $(this).data('waktu');
            
            if(rfid) {
                scanPresensi(rfid, waktu);
                $(this).val('');
            }
        }
    });

    function scanPresensi(rfid, waktu) {
        $.ajax({
            url: '{{ route("presensi.sholat.scan") }}',
            method: 'POST',
            data: {
                rfid_card: rfid,
                waktu_sholat: waktu
            },
            success: function(response) {
                $(`#result_${waktu}`).html(`
                    <div class="alert alert-success alert-sm">
                        <strong>${response.data.user.name}</strong><br>
                        ${response.message}
                    </div>
                `);
                setTimeout(() => location.reload(), 2000);
            },
            error: function(xhr) {
                $(`#result_${waktu}`).html(`
                    <div class="alert alert-danger alert-sm">
                        ${xhr.responseJSON?.message || 'Terjadi kesalahan'}
                    </div>
                `);
            }
        });
    }

    function updateKeterangan(id) {
        $('#presensi_id').val(id);
        $('#modalKeterangan').modal('show');
    }

    function simpanKeterangan() {
        let presensiId = $('#presensi_id').val();
        let keterangan = $('input[name="keterangan"]:checked').val();

        if(!keterangan) {
            alert('Pilih keterangan terlebih dahulu!');
            return;
        }

        $.ajax({
            url: '{{ route("presensi.sholat.update") }}',
            method: 'POST',
            data: {
                presensi_id: presensiId,
                keterangan: keterangan
            },
            success: function(response) {
                $('#modalKeterangan').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + xhr.responseJSON?.message);
            }
        });
    }

    // Auto refresh jadwal setiap 60 detik
    setInterval(function() {
        $.get('{{ route("presensi.sholat.jadwal") }}', function(response) {
            if(response.success && response.jadwal) {
                // Update tampilan jadwal jika diperlukan
            }
        });
    }, 60000);
</script>
@endsection