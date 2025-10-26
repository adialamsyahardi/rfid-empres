<!-- resources/views/presensi/kustom.blade.php - UPDATED -->
@extends('layouts.app')

@section('title', 'Presensi Kustom')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Presensi Kustom</h2>
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('presensi.kustom.jadwal.index') }}" class="btn btn-primary">
            <i class="fas fa-calendar-plus me-2"></i>Kelola Jadwal
        </a>
        @endif
    </div>
    
    <!-- Jadwal Hari Ini -->
    @if($jadwalHariIni->count() > 0)
    <div class="row mb-4">
        @foreach($jadwalHariIni as $jadwal)
        <div class="col-md-4 mb-3">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar-check me-2"></i>
                        {{ $jadwal->nama_kegiatan }}
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Waktu:</strong> {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}
                    </p>
                    @if($jadwal->keterangan)
                    <p class="mb-3"><small>{{ $jadwal->keterangan }}</small></p>
                    @endif
                    
                    <input type="text" 
                           class="form-control scan-input mb-2" 
                           data-jadwal-id="{{ $jadwal->id }}" 
                           placeholder="Scan RFID untuk presensi...">
                    <div id="result_{{ $jadwal->id }}"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Tidak ada jadwal kegiatan untuk hari ini. 
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('presensi.kustom.jadwal.index') }}">Tambah Jadwal</a>
        @endif
    </div>
    @endif

    <!-- Daftar Presensi Hari Ini -->
    <div class="card">
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
                            <th>Kegiatan</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Jam Scan</th>
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
                            <td>{{ $p->kepentingan }}</td>
                            <td>{{ $p->jam_mulai }}</td>
                            <td>{{ $p->jam_selesai }}</td>
                            <td>
                                {{ $p->jam_scan ?? '-' }}
                                @if($p->terlambat && $p->jam_scan)
                                    <br><small class="text-danger">(+{{ $p->menit_terlambat }} menit)</small>
                                @endif
                            </td>
                            <td>
                                @if($p->status == 'hadir')
                                    <span class="badge badge-ontime">Tepat Waktu</span>
                                @elseif($p->status == 'terlambat')
                                    <span class="badge badge-terlambat">Terlambat</span>
                                @else
                                    <span class="badge bg-secondary">Tanpa Keterangan</span>
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
                            <td colspan="9" class="text-center">Belum ada presensi hari ini</td>
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
            let jadwalId = $(this).data('jadwal-id');
            
            if(rfid) {
                scanPresensi(rfid, jadwalId);
                $(this).val('');
            }
        }
    });

    function scanPresensi(rfid, jadwalId) {
        $.ajax({
            url: '{{ route("presensi.kustom.scan") }}',
            method: 'POST',
            data: {
                rfid_card: rfid,
                jadwal_id: jadwalId
            },
            success: function(response) {
                let alertClass = response.data.presensi.terlambat ? 'alert-warning' : 'alert-success';
                
                $(`#result_${jadwalId}`).html(`
                    <div class="alert ${alertClass} alert-sm">
                        <strong>${response.data.user.name}</strong><br>
                        ${response.message}
                    </div>
                `);
                setTimeout(() => location.reload(), 2000);
            },
            error: function(xhr) {
                $(`#result_${jadwalId}`).html(`
                    <div class="alert alert-danger alert-sm">
                        ${xhr.responseJSON?.message || 'Terjadi kesalahan'}
                    </div>
                `);
            }
        });
    }

    function updateKeterangan(id, currentKeterangan) {
        $('#presensi_id').val(id);
        $(`input[name="keterangan"][value="${currentKeterangan}"]`).prop('checked', true);
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
            url: '{{ route("presensi.kustom.update") }}',
            method: 'POST',
            data: {
                presensi_id: presensiId,
                keterangan: keterangan
            },
            success: function(response) {
                $('#modalKeterangan').modal('show');
                location.reload();
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + xhr.responseJSON?.message);
            }
        });
    }
</script>
@endsection