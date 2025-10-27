<!-- resources/views/users/index.blade.php -->
@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Kelola User</h2>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah User
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>RFID Card</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Saldo</th>
                            <th>Limit Harian</th>
                            <th>Status Limit</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                        <tr>
                            <td>{{ $users->firstItem() + $index }}</td>
                            <td><code>{{ $user->rfid_card }}</code></td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>Rp {{ number_format($user->saldo, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($user->limit_harian, 0, ',', '.') }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-limit" type="checkbox" 
                                           data-user-id="{{ $user->id }}"
                                           {{ $user->limit_saldo_aktif ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        {{ $user->limit_saldo_aktif ? 'Aktif' : 'Nonaktif' }}
                                    </label>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="hapusUser({{ $user->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada user</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $users->links() }}
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
    $('.toggle-limit').on('change', function() {
        let userId = $(this).data('user-id');
        let status = $(this).is(':checked');
        
        $.ajax({
            url: '{{ route("kantin.toggle-limit") }}',
            method: 'POST',
            data: {
                user_id: userId,
                status: status
            },
            success: function(response) {
                alert(response.message);
            },
            error: function(xhr) {
                alert('Terjadi kesalahan');
                location.reload();
            }
        });
    });

    function hapusUser(id) {
        if(confirm('Yakin ingin menghapus user ini?')) {
            let form = $('#deleteForm');
            form.attr('action', `/users/${id}`);
            form.submit();
        }
    }
</script>
@endsection