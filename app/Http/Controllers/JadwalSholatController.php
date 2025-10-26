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
            // ID Kota untuk Pacet, Mojokerto - menggunakan ID Kabupaten Mojokerto: 1508
            // Jika tidak ada, gunakan ID Kota Mojokerto: 1507
            $idKota = 1508; // Kabupaten Mojokerto (termasuk Pacet)
            
            $bulan = str_pad($request->bulan, 2, '0', STR_PAD_LEFT);
            $url = "https://api.myquran.com/v2/sholat/jadwal/{$idKota}/{$request->tahun}/{$bulan}";
            
            Log::info("Fetching jadwal sholat from: " . $url);
            
            $response = Http::timeout(30)->get($url);

            if (!$response->successful()) {
                Log::error("API Response failed", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data dari API. Status: ' . $response->status()
                ], 500);
            }

            $data = $response->json();
            
            Log::info("API Response", ['data' => $data]);

            if (!isset($data['data']['jadwal']) || empty($data['data']['jadwal'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format data API tidak valid atau jadwal kosong',
                    'debug' => $data
                ], 500);
            }

            $count = 0;
            foreach ($data['data']['jadwal'] as $item) {
                // Parse tanggal dari format API
                $tanggal = isset($item['date']) ? $item['date'] : $item['tanggal'];
                
                // Jika tanggal format "Sabtu, 01/06/2024", parse ke Y-m-d
                if (is_string($tanggal) && strpos($tanggal, '/') !== false) {
                    // Extract date dari format "Sabtu, 01/06/2024"
                    preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $tanggal, $matches);
                    if (count($matches) == 4) {
                        $tanggal = $matches[3] . '-' . $matches[2] . '-' . $matches[1]; // Y-m-d
                    }
                }
                
                JadwalSholat::updateOrCreate(
                    ['tanggal' => $tanggal],
                    [
                        'subuh' => $item['subuh'] ?? $item['imsak'],
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
                'message' => "Berhasil sinkronisasi {$count} jadwal sholat untuk bulan {$request->bulan}/{$request->tahun}",
                'lokasi' => $data['data']['lokasi'] ?? 'Mojokerto'
            ]);

        } catch (\Exception $e) {
            Log::error("Error syncing jadwal sholat", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
