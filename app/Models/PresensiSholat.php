<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PresensiSholat extends Model
{
    protected $table = 'presensi_sholat';
    
    protected $fillable = [
        'user_id',
        'tanggal',
        'waktu_sholat',
        'jam_presensi',
        'keterangan',
        'terlambat',
        'menit_terlambat'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'terlambat' => 'boolean'
    ];

    // ✅ DEFAULT VALUES
    protected $attributes = [
        'keterangan' => 'tanpa_keterangan',
        'terlambat' => false,
        'menit_terlambat' => 0
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper Method
    public static function hitungKeterlambatan($jamAdzan, $jamPresensi)
    {
        $adzan = Carbon::createFromFormat('H:i:s', $jamAdzan);
        $presensi = Carbon::createFromFormat('H:i:s', $jamPresensi);
        
        $selisih = $presensi->diffInMinutes($adzan, false);
        
        $toleransi = PengaturanWaktu::getSholatToleransi();
        $batasToleransi = $toleransi ? $toleransi->toleransi_keterlambatan : 20;
        
        if ($selisih < 0) {
            return [
                'terlambat' => false,
                'menit' => 0
            ];
        }
        
        if ($selisih <= $batasToleransi) {
            return [
                'terlambat' => false,
                'menit' => 0
            ];
        }
        
        return [
            'terlambat' => true,
            'menit' => $selisih
        ];
    }
}