<!-- resources/views/kantin/riwayat.blade.php -->
@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Riwayat Transaksi E-Kantin</h2>
    
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Semua Transaksi</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                            <th>Saldo Sebelum</th>
                            <th>Saldo Sesudah</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $index => $t)
                        <tr>
                            <td>{{ $transaksi->firstItem() + $index }}</td>
                            <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $t->user->name }}</td>
                            <td>
                                <span class="badge bg-{{ $t->jenis == 'topup' ? 'success' : 'danger' }}">
                                    {{ $t->jenis == 'topup' ? 'Top Up' : 'Pembayaran' }}
                                </span>
                            </td>
                            <td class="text-end">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($t->saldo_sebelum, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($t->saldo_sesudah, 0, ',', '.') }}</td>
                            <td>{{ $t->keterangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $transaksi->links() }}
        </div>
    </div>
</div>
@endsection
