<!-- resources/views/presensi/sekolah.blade.php -->
@extends('layouts.app')

@section('title', 'Presensi Sekolah')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Presensi Sekolah</h2>
    
    <!-- Info Pengaturan Waktu -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="alert alert-info">
                <strong><i class="fas fa-clock me-2"></i>Jam Masuk:</strong> 
                {{ $pengaturanMasuk ? $pengaturanMasuk->jam_mulai . ' - ' . $pengaturanMasuk->jam_selesai : 'Belum diatur' }}
                @if($pengaturanMasuk)
                <br><small>Toleransi: {{ $pengaturanMasuk->toleransi_keterlambatan }} menit</small>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="alert alert-warning">
                <strong><i class="fas fa-clock me-2"></i>Jam Pulang:</strong> 
                {{ $pengaturanPulang ? $pengaturanPulang->jam_mulai . ' - ' . $pengaturanPulang->jam_selesai : 'Belum diatur' }}
                @if($pengaturanPulang)
                <br><small>Toleransi: {{ $pengaturanPulang->toleransi_keterlambatan }} menit</small>
                @endif
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Scan Masuk</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Tap kartu RFID untuk presensi masuk
                    </div>
                    <input type="text" id="rfid_masuk" class="form-control form-control-lg" placeholder="Scan kartu RFID..." autofocus>
                    <div id="result_masuk" class="mt-3"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-sign-out-alt me-2"></i>Scan Keluar</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>Tap kartu RFID untuk presensi keluar
                    </div>
                    <input type="text" id="rfid_keluar" class="form-control form-control-lg" placeholder="Scan kartu RFID...">
                    <div id="result_keluar" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Presensi Hari Ini</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
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
                            <td>{{ $p->user->name }}</td>
                            <td>
                                {{ $p->jam_masuk ?? '-' }}
                                @if($p->terlambat_masuk)
                                    <br><small class="text-danger">(+{{ $p->menit_terlambat_masuk }} menit)</small>
                                @endif
                            </td>
                            <td>
                                @if($p->jam_masuk)
                                    <span class="badge {{ $p->terlambat_masuk ? 'badge-terlambat' : 'badge-ontime' }}">
                                        {{ $p->terlambat_masuk ? 'Terlambat' : 'Tepat Waktu' }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                {{ $p->jam_keluar ?? '-' }}
                                @if($p->terlambat_keluar)
                                    <br><small class="text-danger">(+{{ $p->menit_terlambat_keluar }} menit)</small>
                                @endif
                            </td>
                            <td>
                                @if($p->jam_keluar)
                                    <span class="badge {{ $p->terlambat_keluar ? 'badge-terlambat' : 'badge-ontime' }}">
                                        {{ $p->terlambat_keluar ? 'Pulang Terlambat' : 'Tepat Waktu' }}
                                    </span>
                                @else
                                    -
                                @endif
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
                            <td colspan="8" class="text-center">Belum ada presensi hari ini</td>
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
    function scanPresensi(rfid, jenis) {
        $.ajax({
            url: '{{ route("presensi.sekolah.scan") }}',
            method: 'POST',
            data: {
                rfid_card: rfid,
                jenis: jenis
            },
            success: function(response) {
                let resultDiv = jenis === 'masuk' ? '#result_masuk' : '#result_keluar';
                let alertClass = response.data.presensi.terlambat_masuk || response.data.presensi.terlambat_keluar ? 'alert-warning' : 'alert-success';
                
                $(resultDiv).html(`
                    <div class="alert ${alertClass}">
                        <h5>${response.data.user.name}</h5>
                        <p class="mb-0">${response.message}</p>
                    </div>
                `);
                setTimeout(() => location.reload(), 2000);
            },
            error: function(xhr) {
                let resultDiv = jenis === 'masuk' ? '#result_masuk' : '#result_keluar';
                $(resultDiv).html(`
                    <div class="alert alert-danger">
                        ${xhr.responseJSON?.message || 'Terjadi kesalahan'}
                    </div>
                `);
            }
        });
    }

    $('#rfid_masuk').on('keypress', function(e) {
        if(e.which === 13) {
            let rfid = $(this).val().trim();
            if(rfid) {
                scanPresensi(rfid, 'masuk');
                $(this).val('');
            }
        }
    });

    $('#rfid_keluar').on('keypress', function(e) {
        if(e.which === 13) {
            let rfid = $(this).val().trim();
            if(rfid) {
                scanPresensi(rfid, 'keluar');
                $(this).val('');
            }
        }
    });

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
            url: '{{ route("presensi.sekolah.update") }}',
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
    function scanPresensi(rfid, jenis) {
        $.ajax({
            url: '{{ route("presensi.sekolah.scan") }}',
            method: 'POST',
            data: {
                rfid_card: rfid,
                jenis: jenis
            },
            success: function(response) {
                let resultDiv = jenis === 'masuk' ? '#result_masuk' : '#result_keluar';
                $(resultDiv).html(`
                    <div class="alert alert-success">
                        <h5>${response.data.user.name}</h5>
                        <p class="mb-0">${response.message}</p>
                    </div>
                `);
                setTimeout(() => location.reload(), 2000);
            },
            error: function(xhr) {
                let resultDiv = jenis === 'masuk' ? '#result_masuk' : '#result_keluar';
                $(resultDiv).html(`
                    <div class="alert alert-danger">
                        ${xhr.responseJSON?.message || 'Terjadi kesalahan'}
                    </div>
                `);
            }
        });
    }

    $('#rfid_masuk').on('keypress', function(e) {
        if(e.which === 13) {
            let rfid = $(this).val().trim();
            if(rfid) {
                scanPresensi(rfid, 'masuk');
                $(this).val('');
            }
        }
    });

    $('#rfid_keluar').on('keypress', function(e) {
        if(e.which === 13) {
            let rfid = $(this).val().trim();
            if(rfid) {
                scanPresensi(rfid, 'keluar');
                $(this).val('');
            }
        }
    });

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
            url: '{{ route("presensi.sekolah.update") }}',
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
</script>
@endsection