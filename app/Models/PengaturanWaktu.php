<?php
// app/Models/PengaturanWaktu.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanWaktu extends Model
{
    protected $table = 'pengaturan_waktu';
    
    protected $fillable = [
        'jenis', 'nama', 'jam_mulai', 'jam_selesai', 
        'toleransi_keterlambatan', 'aktif'
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'toleransi_keterlambatan' => 'integer',
    ];

    public static function getSekolahMasuk()
    {
        return self::where('jenis', 'sekolah')
            ->where('nama', 'Jam Masuk')
            ->where('aktif', true)
            ->first();
    }

    public static function getSekolahPulang()
    {
        return self::where('jenis', 'sekolah')
            ->where('nama', 'Jam Pulang')
            ->where('aktif', true)
            ->first();
    }

    public static function getSholatToleransi()
    {
        return self::where('jenis', 'sholat')
            ->where('aktif', true)
            ->first();
    }
}
