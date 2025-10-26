<?php
namespace App\Http\Controllers;

use App\Models\PresensiSekolah;
use App\Models\PengaturanWaktu;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresensiSekolahController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $presensi = PresensiSekolah::with('user')
            ->whereDate('tanggal', $today)
            ->latest()
            ->paginate(20);
        
        $pengaturanMasuk = PengaturanWaktu::getSekolahMasuk();
        $pengaturanPulang = PengaturanWaktu::getSekolahPulang();
        
        return view('presensi.sekolah', compact('presensi', 'pengaturanMasuk', 'pengaturanPulang'));
    }

    public function scan(Request $request)
    {
        $request->validate([
            'rfid_card' => 'required|string',
            'jenis' => 'required|in:masuk,keluar',
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
        
        $presensi = PresensiSekolah::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (!$presensi) {
            $presensi = new PresensiSekolah();
            $presensi->user_id = $user->id;
            $presensi->tanggal = $today;
        }

        if ($request->jenis === 'masuk') {
            if ($presensi->jam_masuk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan presensi masuk hari ini!'
                ]);
            }
            
            $presensi->jam_masuk = $jamSekarang;
            $presensi->keterangan = 'hadir';
            
            // Hitung keterlambatan
            $keterlambatan = PresensiSekolah::hitungKeterlambatanMasuk($jamSekarang);
            $presensi->terlambat_masuk = $keterlambatan['terlambat'];
            $presensi->menit_terlambat_masuk = $keterlambatan['menit'];
            
            $pesan = $keterlambatan['terlambat'] 
                ? "Presensi masuk berhasil! Terlambat {$keterlambatan['menit']} menit" 
                : "Presensi masuk berhasil!";
                
        } else {
            if ($presensi->jam_keluar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan presensi keluar hari ini!'
                ]);
            }
            
            $presensi->jam_keluar = $jamSekarang;
            
            // Hitung keterlambatan
            $keterlambatan = PresensiSekolah::hitungKeterlambatanKeluar($jamSekarang);
            $presensi->terlambat_keluar = $keterlambatan['terlambat'];
            $presensi->menit_terlambat_keluar = $keterlambatan['menit'];
            
            $pesan = $keterlambatan['terlambat'] 
                ? "Presensi keluar berhasil! Pulang terlambat {$keterlambatan['menit']} menit" 
                : "Presensi keluar berhasil!";
        }

        $presensi->save();

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
            'presensi_id' => 'required|exists:presensi_sekolah,id',
            'keterangan' => 'required|in:hadir,izin,sakit,tanpa_keterangan'
        ]);

        $presensi = PresensiSekolah::findOrFail($request->presensi_id);
        $presensi->keterangan = $request->keterangan;
        $presensi->save();

        return response()->json([
            'success' => true,
            'message' => 'Keterangan berhasil diperbarui!'
        ]);
    }
}
