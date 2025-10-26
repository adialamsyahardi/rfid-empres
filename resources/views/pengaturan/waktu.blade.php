<!-- resources/views/pengaturan/waktu.blade.php -->
@extends('layouts.app')

@section('title', 'Pengaturan Waktu')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Pengaturan Waktu Presensi</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-plus me-2"></i>Tambah Pengaturan
        </button>
    </div>
    
    <!-- Pengaturan Sekolah -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-school me-2"></i>Pengaturan Presensi Sekolah</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Toleransi (Menit)</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pengaturan->where('jenis', 'sekolah') as $p)
                        <tr>
                            <td><strong>{{ $p->nama }}</strong></td>
                            <td>{{ $p->jam_mulai }}</td>
                            <td>{{ $p->jam_selesai }}</td>
                            <td>{{ $p->toleransi_keterlambatan }} menit</td>
                            <td>
                                <span class="badge bg-{{ $p->aktif ? 'success' : 'secondary' }}">
                                    {{ $p->aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="editPengaturan({{ $p }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pengaturan Sholat -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-mosque me-2"></i>Pengaturan Presensi Sholat</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Catatan:</strong> Waktu presensi sholat dimulai dari jadwal adzan yang sudah tersinkronisasi. 
                Toleransi keterlambatan dihitung setelah waktu adzan.
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Toleransi Keterlambatan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pengaturan->where('jenis', 'sholat') as $p)
                        <tr>
                            <td><strong>{{ $p->nama }}</strong></td>
                            <td>{{ $p->toleransi_keterlambatan }} menit setelah adzan</td>
                            <td>
                                <span class="badge bg-{{ $p->aktif ? 'success' : 'secondary' }}">
                                    {{ $p->aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="editPengaturan({{ $p }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pengaturan Kustom (jika ada) -->
    @if($pengaturan->where('jenis', 'kustom')->count() > 0)
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Pengaturan Presensi Kustom</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Toleransi (Menit)</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pengaturan->where('jenis', 'kustom') as $p)
                        <tr>
                            <td><strong>{{ $p->nama }}</strong></td>
                            <td>{{ $p->jam_mulai }}</td>
                            <td>{{ $p->jam_selesai }}</td>
                            <td>{{ $p->toleransi_keterlambatan }} menit</td>
                            <td>
                                <span class="badge bg-{{ $p->aktif ? 'success' : 'secondary' }}">
                                    {{ $p->aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="editPengaturan({{ $p }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="hapusPengaturan({{ $p->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
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

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pengaturan Waktu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTambah">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jenis</label>
                        <select name="jenis" class="form-select" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="sekolah">Sekolah</option>
                            <option value="sholat">Sholat</option>
                            <option value="kustom">Kustom</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Toleransi Keterlambatan (Menit)</label>
                        <input type="number" name="toleransi_keterlambatan" class="form-control" min="0" required>
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
                <h5 class="modal-title">Edit Pengaturan Waktu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEdit">
                <input type="hidden" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" id="edit_nama" name="nama" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" id="edit_jam_mulai" name="jam_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" id="edit_jam_selesai" name="jam_selesai" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Toleransi Keterlambatan (Menit)</label>
                        <input type="number" id="edit_toleransi" name="toleransi_keterlambatan" class="form-control" min="0" required>
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
            url: '{{ route("pengaturan.waktu.create") }}',
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

    function editPengaturan(data) {
        $('#edit_id').val(data.id);
        $('#edit_nama').val(data.nama);
        $('#edit_jam_mulai').val(data.jam_mulai);
        $('#edit_jam_selesai').val(data.jam_selesai);
        $('#edit_toleransi').val(data.toleransi_keterlambatan);
        $('#edit_aktif').val(data.aktif ? '1' : '0');
        $('#modalEdit').modal('show');
    }

    $('#formEdit').on('submit', function(e) {
        e.preventDefault();
        
        let id = $('#edit_id').val();
        
        $.ajax({
            url: `/pengaturan-waktu/${id}`,
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

    function hapusPengaturan(id) {
        if(confirm('Yakin ingin menghapus pengaturan ini?')) {
            $.ajax({
                url: `/pengaturan-waktu/${id}`,
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