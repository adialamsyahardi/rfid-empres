<?php
// app/Http/Controllers/PresensiKustomController.php - UPDATED
namespace App\Http\Controllers;

use App\Models\PresensiKustom;
use App\Models\JadwalPresensiKustom;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresensiKustomController extends Controller
{
    // Tampilkan halaman presensi kustom
    public function index()
    {
        $today = Carbon::today();
        
        // Jadwal yang aktif hari ini
        $jadwalHariIni = JadwalPresensiKustom::where('tanggal', $today)
            ->where('aktif', true)
            ->orderBy('jam_mulai')
            ->get();
        
        // Presensi hari ini
        $presensi = PresensiKustom::with('user', 'jadwal')
            ->whereDate('tanggal', $today)
            ->latest()
            ->paginate(20);
        
        return view('presensi.kustom', compact('presensi', 'jadwalHariIni'));
    }

    // Halaman kelola jadwal (Admin only)
    public function jadwalIndex()
    {
        $jadwal = JadwalPresensiKustom::orderBy('tanggal', 'desc')
            ->orderBy('jam_mulai')
            ->paginate(20);
        
        return view('presensi.kustom-jadwal', compact('jadwal'));
    }

    // Tambah jadwal baru
    public function storeJadwal(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'nullable|string',
        ]);

        $jadwal = JadwalPresensiKustom::create([
            'nama_kegiatan' => $request->nama_kegiatan,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keterangan' => $request->keterangan,
            'aktif' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil ditambahkan!',
            'data' => $jadwal
        ]);
    }

    // Update jadwal
    public function updateJadwal(Request $request, $id)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'nullable|string',
            'aktif' => 'required|boolean'
        ]);

        $jadwal = JadwalPresensiKustom::findOrFail($id);
        $jadwal->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil diperbarui!'
        ]);
    }

    // Hapus jadwal
    public function destroyJadwal($id)
    {
        $jadwal = JadwalPresensiKustom::findOrFail($id);
        $jadwal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dihapus!'
        ]);
    }

    // Scan RFID untuk presensi
    public function scan(Request $request)
    {
        $request->validate([
            'rfid_card' => 'required|string',
            'jadwal_id' => 'required|exists:jadwal_presensi_kustom,id',
        ]);

        $user = User::where('rfid_card', $request->rfid_card)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu RFID tidak terdaftar!'
            ], 404);
        }

        $jadwal = JadwalPresensiKustom::findOrFail($request->jadwal_id);
        
        // Cek apakah sudah presensi untuk jadwal ini
        $existing = PresensiKustom::where('user_id', $user->id)
            ->where('jadwal_id', $jadwal->id)
            ->whereDate('tanggal', $jadwal->tanggal)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan presensi untuk kegiatan ini!'
            ]);
        }

        $jamSekarang = Carbon::now()->format('H:i:s');
        $jamMulai = Carbon::parse($jadwal->jam_mulai);
        $jamSelesai = Carbon::parse($jadwal->jam_selesai);
        $jamScan = Carbon::parse($jamSekarang);

        // Tentukan status dan keterlambatan
        $status = 'tanpa_keterangan';
        $terlambat = false;
        $menitTerlambat = 0;

        if ($jamScan->between($jamMulai, $jamSelesai)) {
            $status = 'hadir';
        } elseif ($jamScan->greaterThan($jamSelesai)) {
            $status = 'terlambat';
            $terlambat = true;
            $menitTerlambat = $jamScan->diffInMinutes($jamMulai);
        }

        $presensi = PresensiKustom::create([
            'user_id' => $user->id,
            'jadwal_id' => $jadwal->id,
            'tanggal' => $jadwal->tanggal,
            'jam_mulai' => $jadwal->jam_mulai,
            'jam_selesai' => $jadwal->jam_selesai,
            'jam_scan' => $jamSekarang,
            'kepentingan' => $jadwal->nama_kegiatan,
            'status' => $status,
            'terlambat' => $terlambat,
            'menit_terlambat' => $menitTerlambat,
            'keterangan' => $status
        ]);

        $pesan = $status === 'hadir' 
            ? "Presensi berhasil!" 
            : ($status === 'terlambat' 
                ? "Presensi berhasil! Terlambat {$menitTerlambat} menit" 
                : "Presensi tercatat tanpa keterangan");

        return response()->json([
            'success' => true,
            'message' => $pesan,
            'data' => [
                'user' => $user,
                'presensi' => $presensi,
                'jadwal' => $jadwal
            ]
        ]);
    }

    // Update keterangan manual
    public function updateKeterangan(Request $request)
    {
        $request->validate([
            'presensi_id' => 'required|exists:presensi_kustom,id',
            'keterangan' => 'required|in:hadir,izin,sakit,tanpa_keterangan'
        ]);

        $presensi = PresensiKustom::findOrFail($request->presensi_id);
        $presensi->keterangan = $request->keterangan;
        $presensi->save();

        return response()->json([
            'success' => true,
            'message' => 'Keterangan berhasil diperbarui!'
        ]);
    }
}