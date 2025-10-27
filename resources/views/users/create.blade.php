<!-- resources/views/users/create.blade.php -->
@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Tambah User Baru</h2>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Form Tambah User</h5>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('users.store') }}" id="formUser">
                        @csrf
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Cara Mengisi RFID:</strong> Klik di field RFID Card, lalu tap kartu RFID pada reader.
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">RFID Card <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="rfid_card" 
                                       id="rfid_card"
                                       class="form-control form-control-lg" 
                                       value="{{ old('rfid_card') }}" 
                                       placeholder="Scan kartu RFID di sini..."
                                       required 
                                       autofocus
                                       autocomplete="off">
                                <small class="text-muted">Focus di sini, lalu tap kartu RFID</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control" 
                                       value="{{ old('name') }}" 
                                       required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" 
                                       name="email" 
                                       class="form-control" 
                                       value="{{ old('email') }}" 
                                       required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" 
                                       name="password" 
                                       class="form-control" 
                                       required
                                       minlength="6">
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" 
                                       name="tempat_lahir" 
                                       class="form-control" 
                                       value="{{ old('tempat_lahir') }}">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" 
                                       name="tanggal_lahir" 
                                       class="form-control" 
                                       value="{{ old('tanggal_lahir') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Limit Harian (Rp)</label>
                                <input type="number" 
                                       name="limit_harian" 
                                       class="form-control" 
                                       value="{{ old('limit_harian', 10000) }}" 
                                       min="0" 
                                       step="1000">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" 
                                      class="form-control" 
                                      rows="3">{{ old('alamat') }}</textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Simpan User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto focus ke RFID input
        $('#rfid_card').focus();

        // Cegah form submit saat Enter ditekan di input RFID (biar bisa scan dulu)
        $('#rfid_card').on('keypress', function(e) {
            if(e.which === 13) {
                e.preventDefault();
                // Setelah scan, pindah ke input nama
                $('input[name="name"]').focus();
            }
        });

        // Validasi sebelum submit
        $('#formUser').on('submit', function(e) {
            let rfid = $('#rfid_card').val().trim();
            
            if(!rfid || rfid.length < 3) {
                e.preventDefault();
                alert('RFID Card harus diisi! Tap kartu RFID pada reader.');
                $('#rfid_card').focus();
                return false;
            }
        });
    });
</script>
@endsection