<!-- resources/views/presensi/kustom-jadwal.blade.php -->
@extends('layouts.app')

@section('title', 'Kelola Jadwal Presensi Kustom')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Kelola Jadwal Presensi Kustom</h2>
        <div>
            <a href="{{ route('presensi.kustom.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="# modalTambah">
                <i class="fas fa-plus me-2"></i>Tambah Jadwal
            </button>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kegiatan</th>
                            <th>Tanggal</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwal as $index => $j)
                        <tr>
                            <td>{{ $jadwal->firstItem() + $index }}</td>
                            <td><strong>{{ $j->nama_kegiatan }}</strong></td>
                            <td>{{ $j->tanggal->format('d/m/Y') }}</td>
                            <td>{{ $j->jam_mulai }}</td>
                            <td>{{ $j->jam_selesai }}</td>
                            <td>{{ $j->keterangan ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $j->aktif ? 'success' : 'secondary' }}">
                                    {{ $j->aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="editJadwal({{ json_encode($j) }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="hapusJadwal({{ $j->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada jadwal</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $jadwal->links() }}
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jadwal Presensi Kustom</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTambah">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kegiatan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_selesai" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Jadwal Presensi Kustom</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEdit">
                <input type="hidden" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" id="edit_nama_kegiatan" name="nama_kegiatan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" id="edit_tanggal" name="tanggal" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" id="edit_jam_mulai" name="jam_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" id="edit_jam_selesai" name="jam_selesai" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea id="edit_keterangan" name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select id="edit_aktif" name="aktif" class="form-select" required>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('#formTambah').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("presensi.kustom.jadwal.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                alert(response.message);
                location.reload();
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    function editJadwal(data) {
        $('#edit_id').val(data.id);
        $('#edit_nama_kegiatan').val(data.nama_kegiatan);
        $('#edit_tanggal').val(data.tanggal);
        $('#edit_jam_mulai').val(data.jam_mulai);
        $('#edit_jam_selesai').val(data.jam_selesai);
        $('#edit_keterangan').val(data.keterangan);
        $('#edit_aktif').val(data.aktif ? '1' : '0');
        $('#modalEdit').modal('show');
    }

    $('#formEdit').on('submit', function(e) {
        e.preventDefault();
        
        let id = $('#edit_id').val();
        
        $.ajax({
            url: `/presensi-kustom/jadwal/${id}`,
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                alert(response.message);
                location.reload();
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    function hapusJadwal(id) {
        if(confirm('Yakin ingin menghapus jadwal ini?')) {
            $.ajax({
                url: `/presensi-kustom/jadwal/${id}`,
                method: 'DELETE',
                success: function(response) {
                    alert(response.message);
                    location.reload();
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan: ' + (xhr.responseJSON?.message || 'Unknown error'));
                }
            });
        }
    }
</script>
@endsection