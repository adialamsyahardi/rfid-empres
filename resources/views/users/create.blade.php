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

                    <form method="POST" action="{{ route('users.store') }}" id="formUser" autocomplete="off">
                        @csrf
                        
                        <!-- ✅ DUMMY INPUT UNTUK TRICK BROWSER AUTOCOMPLETE -->
                        <input type="text" name="fake_email" style="display:none;" tabindex="-1" autocomplete="email">
                        <input type="password" name="fake_password" style="display:none;" tabindex="-1" autocomplete="current-password">
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Cara Mengisi RFID:</strong> Klik di field RFID Card, lalu tap kartu RFID pada reader.
                        </div>

                        <!-- ✅ RFID & NAMA -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">RFID Card <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="rfid_card" 
                                       id="rfid_card"
                                       class="form-control form-control-lg" 
                                       value="{{ old('rfid_card') }}" 
                                       placeholder="Tap kartu RFID di sini..."
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
                                       placeholder="Contoh: Ahmad Rizki Maulana"
                                       autocomplete="off"
                                       required>
                            </div>
                        </div>

                        <!-- ✅ EMAIL & PASSWORD (DISABLE AUTOCOMPLETE) -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" 
                                       name="email" 
                                       id="email"
                                       class="form-control" 
                                       value="{{ old('email') }}" 
                                       placeholder="contoh@email.com"
                                       autocomplete="off"
                                       readonly 
                                       onfocus="this.removeAttribute('readonly');"
                                       required>
                                <small class="text-muted">Email untuk login user</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" 
                                       name="password" 
                                       id="password"
                                       class="form-control" 
                                       placeholder="Minimal 6 karakter"
                                       autocomplete="new-password"
                                       readonly 
                                       onfocus="this.removeAttribute('readonly');"
                                       required
                                       minlength="6">
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>
                        </div>

                        <!-- ✅ ROLE & JENIS KELAMIN -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-select" required>
                                    <option value="">-- Pilih Role --</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                        🔑 Admin (Akses Penuh)
                                    </option>
                                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>
                                        👤 User (Akses Terbatas)
                                    </option>
                                </select>
                                <small class="text-muted">
                                    Admin: Full akses | User: Hanya presensi
                                </small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>
                                        👨 Laki-laki
                                    </option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>
                                        👩 Perempuan
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- ✅ TEMPAT & TANGGAL LAHIR -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" 
                                       name="tempat_lahir" 
                                       class="form-control" 
                                       value="{{ old('tempat_lahir') }}"
                                       placeholder="Contoh: Jakarta"
                                       autocomplete="off">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" 
                                       name="tanggal_lahir" 
                                       class="form-control" 
                                       value="{{ old('tanggal_lahir') }}"
                                       autocomplete="off">
                            </div>
                        </div>

                        <!-- ✅ LIMIT HARIAN & ALAMAT -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Limit Harian (Rp)</label>
                                <input type="number" 
                                       name="limit_harian" 
                                       class="form-control" 
                                       value="{{ old('limit_harian', 10000) }}" 
                                       min="0" 
                                       step="1000"
                                       placeholder="10000"
                                       autocomplete="off">
                                <small class="text-muted">Kosongkan jika tidak ada limit</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" 
                                          class="form-control" 
                                          rows="3"
                                          placeholder="Contoh: Jl. Raya No. 123, Jakarta Selatan"
                                          autocomplete="off">{{ old('alamat') }}</textarea>
                            </div>
                        </div>

                        <!-- ✅ BUTTON SUBMIT -->
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

        // ✅ Clear email & password saat page load (force kosongkan)
        setTimeout(function() {
            $('#email').val('');
            $('#password').val('');
        }, 100);

        // Cegah form submit saat Enter ditekan di input RFID (biar bisa scan dulu)
        $('#rfid_card').on('keypress', function(e) {
            if(e.which === 13) {
                e.preventDefault();
                // Setelah scan, pindah ke input nama
                $('input[name="name"]').focus();
            }
        });

        // ✅ Validasi sebelum submit
        $('#formUser').on('submit', function(e) {
            let rfid = $('#rfid_card').val().trim();
            let role = $('#role').val();
            let email = $('#email').val().trim();
            let password = $('#password').val();
            
            // Cek RFID
            if(!rfid || rfid.length < 3) {
                e.preventDefault();
                alert('⚠️ RFID Card harus diisi! Tap kartu RFID pada reader.');
                $('#rfid_card').focus();
                return false;
            }

            // Cek Email
            if(!email) {
                e.preventDefault();
                alert('⚠️ Email harus diisi!');
                $('#email').focus();
                return false;
            }

            // Cek Password
            if(!password || password.length < 6) {
                e.preventDefault();
                alert('⚠️ Password minimal 6 karakter!');
                $('#password').focus();
                return false;
            }

            // ✅ Cek Role
            if(!role) {
                e.preventDefault();
                alert('⚠️ Role harus dipilih! Pilih Admin atau User.');
                $('#role').focus();
                return false;
            }

            // ✅ Konfirmasi sebelum submit
            let roleName = role === 'admin' ? 'Administrator' : 'User Biasa';
            if(!confirm(`Simpan user dengan role ${roleName}?`)) {
                e.preventDefault();
                return false;
            }
        });

        // ✅ Auto-capitalize nama
        $('input[name="name"]').on('blur', function() {
            let name = $(this).val();
            $(this).val(name.replace(/\b\w/g, l => l.toUpperCase()));
        });

        // ✅ Highlight role saat dipilih
        $('#role').on('change', function() {
            let role = $(this).val();
            if(role === 'admin') {
                $(this).removeClass('border-primary').addClass('border-danger');
            } else if(role === 'user') {
                $(this).removeClass('border-danger').addClass('border-primary');
            } else {
                $(this).removeClass('border-danger border-primary');
            }
        });

        // ✅ Prevent paste di password (optional, untuk security)
        $('#password').on('paste', function(e) {
            e.preventDefault();
            alert('⚠️ Paste password tidak diperbolehkan!');
        });
    });
</script>
@endsection