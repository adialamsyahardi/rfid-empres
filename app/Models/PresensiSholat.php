<?php
// Update app/Models/PresensiSholat.php - tambahkan method
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PresensiSholat extends Model
{
    protected $table = 'presensi_sholat';
    
    protected $fillable = [
        'user_id', 'tanggal', 'waktu_sholat', 'jam_presensi', 
        'keterangan', 'terlambat', 'menit_terlambat'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'terlambat' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Hitung keterlambatan sholat
    public static function hitungKeterlambatan($jamAdzan, $jamScan)
    {
        $pengaturan = PengaturanWaktu::getSholatToleransi();
        $toleransi = $pengaturan ? $pengaturan->toleransi_keterlambatan : 20;

        $adzan = Carbon::parse($jamAdzan);
        $batasWaktu = $adzan->copy()->addMinutes($toleransi);
        $scan = Carbon::parse($jamScan);

        if ($scan->greaterThan($batasWaktu)) {
            $menit = $scan->diffInMinutes($adzan);
            return ['terlambat' => true, 'menit' => $menit];
        }

        return ['terlambat' => false, 'menit' => 0];
    }
}
