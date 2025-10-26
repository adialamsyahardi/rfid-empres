<?php
// app/Http/Controllers/PresensiKustomController.php
namespace App\Http\Controllers;

use App\Models\PresensiKustom;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresensiKustomController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $presensi = PresensiKustom::with('user')
            ->whereDate('tanggal', $today)
            ->latest()
            ->paginate(20);
        
        return view('presensi.kustom', compact('presensi'));
    }

    public function scan(Request $request)
    {
        $request->validate([
            'rfid_card' => 'required|string',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'kepentingan' => 'required|string|max:255',
        ]);

        $user = User::where('rfid_card', $request->rfid_card)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu RFID tidak terdaftar!'
            ], 404);
        }

        $jamSekarang = Carbon::now()->format('H:i:s');
        $jamMulai = Carbon::parse($request->jam_mulai);
        $jamSelesai = Carbon::parse($request->jam_selesai);
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
            'tanggal' => Carbon::today(),
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'jam_scan' => $jamSekarang,
            'kepentingan' => $request->kepentingan,
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
                'presensi' => $presensi
            ]
        ]);
    }

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
