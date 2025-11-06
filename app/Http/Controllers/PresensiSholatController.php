<?php
namespace App\Http\Controllers;

use App\Models\PresensiSholat;
use App\Models\JadwalSholat;
use App\Models\PengaturanWaktu;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PresensiSholatController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // ✅ HAPUS PAGINATION
        $presensi = PresensiSholat::with('user')
            ->whereDate('tanggal', $today)
            ->orderBy('jam_presensi', 'asc')
            ->get();
        
        $jadwal = JadwalSholat::whereDate('tanggal', $today)->first();
        $toleransi = PengaturanWaktu::getSholatToleransi();
        
        // Format hari dan tanggal dalam bahasa Indonesia
        $hari = Carbon::parse($today)->locale('id')->translatedFormat('l');
        $tanggal = Carbon::parse($today)->locale('id')->translatedFormat('d F Y');
        
        return view('presensi.sholat', compact('presensi', 'jadwal', 'toleransi', 'hari', 'tanggal'));
    }

    public function scan(Request $request)
    {
        $request->validate([
            'rfid_card' => 'required|string',
            'waktu_sholat' => 'required|in:subuh,dzuhur,ashar,maghrib,isya',
        ]);

        $user = User::where('rfid_card', $request->rfid_card)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu RFID tidak terdaftar!'
            ], 404);
        }

        $today = Carbon::today();
        $jamSekarang = Carbon::now()->format('H:i:s');
        
        $existing = PresensiSholat::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->where('waktu_sholat', $request->waktu_sholat)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan presensi sholat ' . ucfirst($request->waktu_sholat) . ' hari ini!',
                'data' => [
                    'user' => $user,
                    'presensi' => $existing
                ]
            ], 400);
        }

        // Ambil jadwal sholat
        $jadwal = JadwalSholat::whereDate('tanggal', $today)->first();
        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal sholat hari ini belum tersedia!'
            ], 404);
        }

        $jamAdzan = $jadwal->{$request->waktu_sholat};
        
        // Hitung keterlambatan
        $keterlambatan = PresensiSholat::hitungKeterlambatan($jamAdzan, $jamSekarang);

        $presensi = PresensiSholat::create([
            'user_id' => $user->id,
            'tanggal' => $today,
            'waktu_sholat' => $request->waktu_sholat,
            'jam_presensi' => $jamSekarang,
            'keterangan' => 'hadir',
            'terlambat' => $keterlambatan['terlambat'],
            'menit_terlambat' => $keterlambatan['menit']
        ]);

        $pesan = $keterlambatan['terlambat'] 
            ? "Presensi berhasil! Terlambat {$keterlambatan['menit']} menit dari adzan" 
            : "Presensi sholat berhasil!";

        return response()->json([
            'success' => true,
            'message' => $pesan,
            'data' => [
                'user' => $user,
                'presensi' => $presensi
            ]
        ]);
    }

    // ✅ GANTI METHOD NAME
    public function update(Request $request)
    {
        $request->validate([
            'presensi_id' => 'required|exists:presensi_sholat,id',
            'keterangan' => 'required|in:hadir,izin,sakit,tanpa_keterangan'
        ]);

        $presensi = PresensiSholat::findOrFail($request->presensi_id);
        $presensi->keterangan = $request->keterangan;
        $presensi->save();

        return response()->json([
            'success' => true,
            'message' => 'Keterangan berhasil diubah!',
            'data' => [
                'presensi' => $presensi->load('user')
            ]
        ]);
    }

    public function getJadwal()
    {
        $today = Carbon::today();
        $jadwal = JadwalSholat::whereDate('tanggal', $today)->first();
        $toleransi = PengaturanWaktu::getSholatToleransi();
        
        $hari = Carbon::parse($today)->locale('id')->translatedFormat('l');
        $tanggal = Carbon::parse($today)->locale('id')->translatedFormat('d F Y');
        
        return response()->json([
            'success' => true,
            'jadwal' => $jadwal,
            'toleransi' => $toleransi ? $toleransi->toleransi_keterlambatan : 20,
            'hari' => $hari,
            'tanggal' => $tanggal
        ]);
    }

    public function getLatestPresensi()
    {
        $today = Carbon::today();
        $presensi = PresensiSholat::with('user')
            ->whereDate('tanggal', $today)
            ->latest()
            ->get();
        
        $allUsers = User::where('role', 'user')->orderBy('name')->get();
        $presensiGrouped = $presensi->groupBy('user_id');
        
        $jadwal = JadwalSholat::whereDate('tanggal', $today)->first();
        
        return response()->json([
            'success' => true,
            'presensi' => $presensi,
            'all_users' => $allUsers,
            'presensi_grouped' => $presensiGrouped,
            'jadwal' => $jadwal
        ]);
    }

    public function export(Request $request)
    {
        $tanggal = $request->tanggal ?? Carbon::today();
        
        $presensi = PresensiSholat::with('user')
            ->whereDate('tanggal', $tanggal)
            ->orderBy('waktu_sholat')
            ->orderBy('jam_presensi')
            ->get();
        
        $jadwal = JadwalSholat::whereDate('tanggal', $tanggal)->first();
        
        $filename = "Presensi_Sholat_" . Carbon::parse($tanggal)->format('d-m-Y') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() use ($presensi, $jadwal) {
            $file = fopen('php://output', 'w');
            
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, [
                'No', 
                'Nama', 
                'Waktu Sholat', 
                'Jam Adzan',
                'Jam Presensi', 
                'Status', 
                'Keterangan', 
                'Terlambat (menit)'
            ]);
            
            foreach ($presensi as $index => $p) {
                $jamAdzan = $jadwal ? $jadwal->{$p->waktu_sholat} : '-';
                
                fputcsv($file, [
                    $index + 1,
                    $p->user->name,
                    ucfirst($p->waktu_sholat),
                    $jamAdzan,
                    $p->jam_presensi,
                    $p->terlambat ? 'Terlambat' : 'Tepat Waktu',
                    ucfirst(str_replace('_', ' ', $p->keterangan)),
                    $p->menit_terlambat ?? 0
                ]);
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }
    // ✅ TAMBAH METHOD BARU - Store presensi manual (untuk yang belum absen)
public function storeManual(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'waktu_sholat' => 'required|in:subuh,dzuhur,ashar,maghrib,isya',
        'keterangan' => 'required|in:izin,sakit,tanpa_keterangan'
    ]);

    $today = Carbon::today();
    
    // Cek apakah sudah ada presensi
    $existing = PresensiSholat::where('user_id', $request->user_id)
        ->whereDate('tanggal', $today)
        ->where('waktu_sholat', $request->waktu_sholat)
        ->first();

    if ($existing) {
        return response()->json([
            'success' => false,
            'message' => 'Presensi untuk waktu sholat ini sudah ada!'
        ], 400);
    }

    $user = User::findOrFail($request->user_id);
    $jadwal = JadwalSholat::whereDate('tanggal', $today)->first();
    
    if (!$jadwal) {
        return response()->json([
            'success' => false,
            'message' => 'Jadwal sholat hari ini belum tersedia!'
        ], 404);
    }

    // Create presensi dengan keterangan
    $presensi = PresensiSholat::create([
        'user_id' => $request->user_id,
        'tanggal' => $today,
        'waktu_sholat' => $request->waktu_sholat,
        'jam_presensi' => null, // NULL karena tidak scan
        'keterangan' => $request->keterangan,
        'terlambat' => false,
        'menit_terlambat' => 0
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Keterangan berhasil ditambahkan!',
        'data' => [
            'user' => $user,
            'presensi' => $presensi
        ]
    ]);
}
}