<?php
// app/Http/Controllers/PresensiSholatController.php
namespace App\Http\Controllers;

use App\Models\PresensiSholat;
use App\Models\JadwalSholat;
use App\Models\PengaturanWaktu;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresensiSholatController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $presensi = PresensiSholat::with('user')
            ->whereDate('tanggal', $today)
            ->latest()
            ->paginate(20);
        
        $jadwal = JadwalSholat::whereDate('tanggal', $today)->first();
        $toleransi = PengaturanWaktu::getSholatToleransi();
        
        return view('presensi.sholat', compact('presensi', 'jadwal', 'toleransi'));
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
                'message' => 'Anda sudah melakukan presensi sholat ' . $request->waktu_sholat . ' hari ini!'
            ]);
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

    public function updateKeterangan(Request $request)
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
            'message' => 'Keterangan berhasil diperbarui!'
        ]);
    }

    public function getJadwal()
    {
        $today = Carbon::today();
        $jadwal = JadwalSholat::whereDate('tanggal', $today)->first();
        $toleransi = PengaturanWaktu::getSholatToleransi();
        
        return response()->json([
            'success' => true,
            'jadwal' => $jadwal,
            'toleransi' => $toleransi ? $toleransi->toleransi_keterlambatan : 20
        ]);
    }
}
