<!-- resources/views/presensi/sholat.blade.php -->
@extends('layouts.app')

@section('title', 'Presensi Sholat')

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
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-mosque me-2"></i>
                        Presensi {{ ucfirst($waktu) }}
                    </h6>
                </div>
                <div class="card-body">
                    <input type="text" class="form-control scan-input" data-waktu="{{ $waktu }}" placeholder="Scan kartu RFID...">
                    <div id="result_{{ $waktu }}" class="mt-2"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Presensi Sholat Hari Ini</h5>
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
                            <td>{{ $presensi->firstItem() + $index }}</td>
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
            {{ $presensi->links() }}
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
