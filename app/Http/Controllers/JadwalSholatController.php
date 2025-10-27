<?php
// app/Http/Controllers/JadwalSholatController.php
namespace App\Http\Controllers;

use App\Models\JadwalSholat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            // ID Kota Mojokerto: 1635
            $idKota = 1635;
            
            // Format: TANPA leading zero
            $bulan = (int)$request->bulan;
            $tahun = (int)$request->tahun;
            
            $url = "https://api.myquran.com/v2/sholat/jadwal/{$idKota}/{$tahun}/{$bulan}";
            
            Log::info("Fetching jadwal sholat", [
                'url' => $url,
                'kota_id' => $idKota,
                'tahun' => $tahun,
                'bulan' => $bulan
            ]);
            
            $response = Http::timeout(30)->get($url);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data dari API. Status: ' . $response->status(),
                    'debug' => [
                        'url' => $url,
                        'status' => $response->status(),
                        'response' => $response->body()
                    ]
                ], 500);
            }

            $data = $response->json();

            if (!isset($data['status']) || $data['status'] !== true) {
                return response()->json([
                    'success' => false,
                    'message' => 'Response API tidak valid: ' . ($data['message'] ?? 'Unknown error'),
                    'debug' => ['data' => $data]
                ], 500);
            }

            if (!isset($data['data']['jadwal']) || empty($data['data']['jadwal'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal kosong atau belum tersedia',
                    'debug' => ['data' => $data]
                ], 500);
            }

            $count = 0;
            
            foreach ($data['data']['jadwal'] as $item) {
                try {
                    $tanggal = $item['date'] ?? null;
                    
                    if (!$tanggal) continue;
                    
                    $tanggalCarbon = Carbon::parse($tanggal);
                    
                    JadwalSholat::updateOrCreate(
                        ['tanggal' => $tanggalCarbon->format('Y-m-d')],
                        [
                            'subuh' => $item['subuh'] ?? '04:30',
                            'dzuhur' => $item['dzuhur'] ?? '12:00',
                            'ashar' => $item['ashar'] ?? '15:00',
                            'maghrib' => $item['maghrib'] ?? '18:00',
                            'isya' => $item['isya'] ?? '19:00'
                        ]
                    );
                    $count++;
                } catch (\Exception $e) {
                    Log::error("Error item", ['error' => $e->getMessage()]);
                }
            }

            if ($count === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data yang berhasil disimpan'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil sinkronisasi {$count} jadwal sholat untuk bulan {$request->bulan}/{$request->tahun}",
                'data' => [
                    'total' => $count,
                    'lokasi' => $data['data']['lokasi'] ?? 'Kediri',
                    'daerah' => $data['data']['daerah'] ?? 'Jawa Timur',
                    'note' => 'Menggunakan jadwal Kediri (terdekat dengan Mojokerto/Pacet)'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Error syncing", [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}