<!-- resources/views/jadwal-sholat/index.blade.php -->
@extends('layouts.app')

@section('title', 'Jadwal Sholat')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Kelola Jadwal Sholat</h2>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-sync me-2"></i>Sinkronisasi Jadwal</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Sinkronisasi jadwal sholat dari API Kemenag untuk wilayah Pacet, Mojokerto
                    </div>
                    
                    <form id="formSync">
                        <div class="mb-3">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-select" required>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($i)->locale('id')->monthName }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tahun</label>
                            <select name="tahun" class="form-select" required>
                                @for($i = date('Y') - 1; $i <= date('Y') + 1; $i++)
                                    <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-download me-2"></i>Sinkronisasi Sekarang
                        </button>
                    </form>
                    
                    <div id="syncResult" class="mt-3"></div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Lokasi: Pacet, Mojokerto
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Sumber: API Kemenag
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Update otomatis per bulan
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Daftar Jadwal Sholat</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Subuh</th>
                                    <th>Dzuhur</th>
                                    <th>Ashar</th>
                                    <th>Maghrib</th>
                                    <th>Isya</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jadwal as $j)
                                <tr class="{{ $j->tanggal->isToday() ? 'table-success' : '' }}">
                                    <td>
                                        {{ $j->tanggal->format('d/m/Y') }}
                                        @if($j->tanggal->isToday())
                                            <span class="badge bg-success">Hari Ini</span>
                                        @endif
                                    </td>
                                    <td>{{ $j->subuh }}</td>
                                    <td>{{ $j->dzuhur }}</td>
                                    <td>{{ $j->ashar }}</td>
                                    <td>{{ $j->maghrib }}</td>
                                    <td>{{ $j->isya }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada jadwal. Silakan lakukan sinkronisasi.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $jadwal->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('#formSync').on('submit', function(e) {
        e.preventDefault();
        
        let btn = $(this).find('button[type="submit"]');
        let originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Sinkronisasi...');
        
        $.ajax({
            url: '{{ route("jadwal-sholat.sync") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#syncResult').html(`
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        ${response.message}
                    </div>
                `);
                setTimeout(() => location.reload(), 2000);
            },
            error: function(xhr) {
                $('#syncResult').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle me-2"></i>
                        ${xhr.responseJSON?.message || 'Terjadi kesalahan'}
                    </div>
                `);
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
</script>
@endsection
