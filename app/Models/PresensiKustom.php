<?php
// UPDATE app/Models/PresensiKustom.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PresensiKustom extends Model
{
    protected $table = 'presensi_kustom';
    
    protected $fillable = [
        'user_id', 'jadwal_id', 'tanggal', 'jam_mulai', 'jam_selesai', 
        'jam_scan', 'kepentingan', 'terlambat', 
        'menit_terlambat', 'status', 'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'terlambat' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalPresensiKustom::class, 'jadwal_id');
    }

    // Hitung keterlambatan
    public function hitungKeterlambatan()
    {
        if (!$this->jam_scan) {
            return 0;
        }

        $jamMulai = Carbon::parse($this->jam_mulai);
        $jamScan = Carbon::parse($this->jam_scan);

        if ($jamScan->greaterThan($jamMulai)) {
            return $jamScan->diffInMinutes($jamMulai);
        }

        return 0;
    }
}