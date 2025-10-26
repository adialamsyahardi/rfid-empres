<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Dashboard</h2>
    
    <div class="row">
        <div class="col-md-3">
            <div class="card stat-card bg-primary-custom">
                <i class="fas fa-users fa-3x mb-3"></i>
                <h3>{{ $total_users }}</h3>
                <p class="mb-0">Total User</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-success-custom">
                <i class="fas fa-clipboard-check fa-3x mb-3"></i>
                <h3>{{ $presensi_sekolah_hari_ini }}</h3>
                <p class="mb-0">Presensi Sekolah Hari Ini</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-warning-custom">
                <i class="fas fa-mosque fa-3x mb-3"></i>
                <h3>{{ $presensi_sholat_hari_ini }}</h3>
                <p class="mb-0">Presensi Sholat Hari Ini</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-info-custom">
                <i class="fas fa-money-bill-wave fa-3x mb-3"></i>
                <h3>Rp {{ number_format($total_saldo_sistem, 0, ',', '.') }}</h3>
                <p class="mb-0">Total Saldo Sistem</p>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Presensi Terkini</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Keluar</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($presensi_terkini as $p)
                                <tr>
                                    <td>{{ $p->user->name }}</td>
                                    <td>{{ $p->jam_masuk ?? '-' }}</td>
                                    <td>{{ $p->jam_keluar ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $p->keterangan == 'hadir' ? 'success' : ($p->keterangan == 'izin' ? 'info' : ($p->keterangan == 'sakit' ? 'warning' : 'secondary')) }}">
                                            {{ ucfirst($p->keterangan) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada presensi hari ini</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Transaksi Terkini</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksi_terkini as $t)
                                <tr>
                                    <td>{{ $t->user->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $t->jenis == 'topup' ? 'success' : 'danger' }}">
                                            {{ ucfirst($t->jenis) }}
                                        </span>
                                    </td>
                                    <td>Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
                                    <td>{{ $t->created_at->format('H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada transaksi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
