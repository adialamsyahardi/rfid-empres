<?php
// app/Http/Controllers/JadwalSholatController.php
namespace App\Http\Controllers;

use App\Models\JadwalSholat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class JadwalSholatController extends Controller
{
    public function index()
    {
        $jadwal = JadwalSholat::orderBy('tanggal', 'desc')->paginate(31);
        return view('jadwal-sholat.index', compact('jadwal'));
    }

    public function syncJadwal(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2100'
        ]);

        try {
            // ID Kota Mojokerto: 1507 (sesuai data Kemenag)
            $url = "https://api.myquran.com/v2/sholat/jadwal/1632/{$request->tahun}/{$request->bulan}";
            
            $response = Http::timeout(30)->get($url);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data dari API'
                ], 500);
            }

            $data = $response->json();

            if (!isset($data['data']['jadwal'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format data API tidak valid'
                ], 500);
            }

            $count = 0;
            foreach ($data['data']['jadwal'] as $item) {
                JadwalSholat::updateOrCreate(
                    ['tanggal' => $item['date']],
                    [
                        'subuh' => $item['subuh'],
                        'dzuhur' => $item['dzuhur'],
                        'ashar' => $item['ashar'],
                        'maghrib' => $item['maghrib'],
                        'isya' => $item['isya']
                    ]
                );
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil sinkronisasi {$count} jadwal sholat untuk bulan {$request->bulan}/{$request->tahun}"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
