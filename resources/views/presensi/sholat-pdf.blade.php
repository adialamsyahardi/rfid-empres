<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Presensi Sholat</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2c3e50;
        }
        
        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
            color: #2c3e50;
            font-weight: bold;
        }
        
        .header h2 {
            font-size: 14px;
            color: #27ae60;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 10px;
            color: #666;
        }
        
        .info-section {
            background: #ecf0f1;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        
        .info-row {
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 180px;
        }
        
        .jadwal-section {
            margin-bottom: 15px;
        }
        
        .jadwal-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        
        .jadwal-table td {
            text-align: center;
            padding: 10px;
            color: white;
            border: 1px solid white;
        }
        
        .jadwal-table h4 {
            font-size: 11px;
            margin-bottom: 3px;
            font-weight: bold;
        }
        
        .jadwal-table p {
            font-size: 13px;
            font-weight: bold;
        }
        
        .statistik-section {
            margin-bottom: 15px;
            padding: 10px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        
        .statistik-section h4 {
            margin-bottom: 8px;
            color: #856404;
            font-size: 12px;
        }
        
        .stat-table {
            width: 100%;
            background: white;
            margin-top: 10px;
            border-collapse: collapse;
        }
        
        .stat-table td {
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
        }
        
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9px;
        }
        
        table.main-table thead {
            background: #34495e;
            color: white;
        }
        
        table.main-table thead th {
            padding: 8px 3px;
            text-align: center;
            border: 1px solid #2c3e50;
            font-size: 9px;
            font-weight: bold;
        }
        
        table.main-table tbody td {
            padding: 5px 3px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
        }
        
        .user-name {
            text-align: left !important;
            font-weight: bold;
            font-size: 9px;
        }
        
        .rfid-code {
            font-size: 7px;
            color: #666;
            font-weight: normal;
        }
        
        .hadir {
            background: #d4edda;
            color: #155724;
        }
        
        .belum {
            background: #f8d7da;
            color: #721c24;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }
        
        .badge-success {
            background: #28a745;
            color: white;
        }
        
        .badge-info {
            background: #17a2b8;
            color: white;
        }
        
        .badge-warning {
            background: #ffc107;
            color: #000;
        }
        
        .badge-secondary {
            background: #6c757d;
            color: white;
        }
        
        .keterangan-section {
            margin-top: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            color: #666;
            page-break-inside: avoid;
        }

        @page {
            margin: 15px;
        }

        h3 {
            margin-bottom: 10px;
            color: #2c3e50;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header">
        <h1>LAPORAN PRESENSI SHOLAT</h1>
        <h2>{{ $hari ?? 'N/A' }}, {{ $tanggal ?? 'N/A' }}</h2>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }} WIB</p>
    </div>
    
    <!-- INFO SECTION -->
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Total Santri/User:</span>
            <span><strong>{{ $totalUser ?? 0 }}</strong> orang</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Presensi Hadir:</span>
            <span><strong>{{ $totalPresensi ?? 0 }}</strong> kali</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Belum Absen:</span>
            <span><strong>{{ $totalBelum ?? 0 }}</strong> kali</span>
        </div>
        <div class="info-row">
            <span class="info-label">Batas Keterlambatan:</span>
            <span><strong>{{ $toleransi ? $toleransi->toleransi_keterlambatan : 20 }}</strong> menit setelah adzan</span>
        </div>
    </div>
    
    <!-- JADWAL SHOLAT -->
    @if(isset($jadwal) && $jadwal)
    <div class="jadwal-section">
        <h3>Jadwal Sholat Hari Ini</h3>
        <table class="jadwal-table">
            <tr>
                <td style="background: #667eea; width: 20%;">
                    <h4>Subuh</h4>
                    <p>{{ $jadwal->subuh }}</p>
                </td>
                <td style="background: #f5576c; width: 20%;">
                    <h4>Dzuhur</h4>
                    <p>{{ $jadwal->dzuhur }}</p>
                </td>
                <td style="background: #00b4d8; width: 20%;">
                    <h4>Ashar</h4>
                    <p>{{ $jadwal->ashar }}</p>
                </td>
                <td style="background: #fa709a; width: 20%;">
                    <h4>Maghrib</h4>
                    <p>{{ $jadwal->maghrib }}</p>
                </td>
                <td style="background: #330867; width: 20%;">
                    <h4>Isya</h4>
                    <p>{{ $jadwal->isya }}</p>
                </td>
            </tr>
        </table>
    </div>
    @endif
    
    <!-- STATISTIK PER WAKTU -->
    <div class="statistik-section">
        <h4>Statistik Kehadiran Per Waktu Sholat</h4>
        <table class="stat-table">
            <tr>
                @foreach($waktuSholat ?? [] as $waktu)
                <td style="width: 20%;">
                    <strong style="font-size: 10px;">{{ ucfirst($waktu) }}</strong><br>
                    <small style="color: #28a745; font-size: 9px;">✓ Hadir: {{ $statistikPerWaktu[$waktu]['hadir'] ?? 0 }}</small><br>
                    <small style="color: #dc3545; font-size: 9px;">✗ Belum: {{ $statistikPerWaktu[$waktu]['belum'] ?? 0 }}</small>
                </td>
                @endforeach
            </tr>
        </table>
    </div>
    
    <!-- TABEL PRESENSI -->
    <h3>Detail Presensi per Santri/User</h3>
    <table class="main-table">
        <thead>
            <tr>
                <th width="25">No</th>
                <th width="130">Nama & RFID</th>
                <th width="85">Subuh</th>
                <th width="85">Dzuhur</th>
                <th width="85">Ashar</th>
                <th width="85">Maghrib</th>
                <th width="85">Isya</th>
                <th width="50">Total</th>
                <th width="50">%</th>
            </tr>
        </thead>
        <tbody>
            @forelse($allUsers ?? [] as $index => $user)
            @php
                $userPresensi = isset($presensi) ? $presensi->get($user->id, collect()) : collect();
                $totalHadir = 0;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="user-name">
                    {{ $user->name }}<br>
                    <span class="rfid-code">{{ $user->rfid_card }}</span>
                </td>
                @foreach($waktuSholat ?? [] as $waktu)
                    @php
                        $p = $userPresensi->where('waktu_sholat', $waktu)->first();
                        $hadir = $p != null;
                        if ($hadir) $totalHadir++;
                    @endphp
                    <td class="{{ $hadir ? 'hadir' : 'belum' }}">
                        @if($hadir)
                            <strong style="font-size: 9px;">{{ $p->jam_presensi ?? '-' }}</strong>
                            @if($p->terlambat ?? false)
                                <br><small style="color: #dc3545; font-size: 7px;">(+{{ $p->menit_terlambat }}m)</small>
                            @endif
                            <br>
                            <span class="badge badge-{{ $p->keterangan == 'hadir' ? 'success' : ($p->keterangan == 'izin' ? 'info' : ($p->keterangan == 'sakit' ? 'warning' : 'secondary')) }}">
                                @if($p->keterangan == 'hadir')
                                    H
                                @elseif($p->keterangan == 'izin')
                                    I
                                @elseif($p->keterangan == 'sakit')
                                    S
                                @else
                                    A
                                @endif
                            </span>
                        @else
                            <strong style="color: #dc3545; font-size: 16px;">-</strong>
                        @endif
                    </td>
                @endforeach
                <td>
                    <strong style="font-size: 10px;">{{ $totalHadir }}/5</strong>
                </td>
                <td>
                    @php
                        $persentase = $totalHadir > 0 ? ($totalHadir / 5) * 100 : 0;
                    @endphp
                    <strong style="font-size: 10px;">{{ number_format($persentase, 0) }}%</strong>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; padding: 20px; color: #999;">
                    Tidak ada data user
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- KETERANGAN -->
    <div class="keterangan-section">
        <strong style="font-size: 10px;">Keterangan Status:</strong><br>
        <span class="badge badge-success">H</span> = Hadir &nbsp;&nbsp;
        <span class="badge badge-info">I</span> = Izin &nbsp;&nbsp;
        <span class="badge badge-warning">S</span> = Sakit &nbsp;&nbsp;
        <span class="badge badge-secondary">A</span> = Alpa (Tanpa Keterangan)
    </div>
    
    <!-- FOOTER -->
    <div class="footer">
        <p><strong>Dicetak oleh:</strong> {{ auth()->user()->name ?? 'Admin' }} ({{ ucfirst(auth()->user()->role ?? 'admin') }})</p>
        <p>Sistem Presensi RFID &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>