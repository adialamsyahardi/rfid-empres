<?php
// app/Models/JadwalPresensiKustom.php - MODEL BARU
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPresensiKustom extends Model
{
    protected $table = 'jadwal_presensi_kustom';
    
    protected $fillable = [
        'nama_kegiatan', 'tanggal', 'jam_mulai', 'jam_selesai', 
        'keterangan', 'aktif'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'aktif' => 'boolean',
    ];

    public function presensi()
    {
        return $this->hasMany(PresensiKustom::class, 'jadwal_id');
    }
}