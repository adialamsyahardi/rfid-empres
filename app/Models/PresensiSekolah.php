<?php
// Update app/Models/PresensiSekolah.php - tambahkan method
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PresensiSekolah extends Model
{
    protected $table = 'presensi_sekolah';
    
    protected $fillable = [
        'user_id', 'tanggal', 'jam_masuk', 'jam_keluar', 
        'keterangan', 'terlambat_masuk', 'menit_terlambat_masuk',
        'terlambat_keluar', 'menit_terlambat_keluar'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'terlambat_masuk' => 'boolean',
        'terlambat_keluar' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Hitung keterlambatan masuk
    public static function hitungKeterlambatanMasuk($jamScan)
    {
        $pengaturan = PengaturanWaktu::getSekolahMasuk();
        if (!$pengaturan) return ['terlambat' => false, 'menit' => 0];

        $jamMulai = Carbon::parse($pengaturan->jam_mulai);
        $jamSelesai = Carbon::parse($pengaturan->jam_selesai);
        $scan = Carbon::parse($jamScan);

        if ($scan->greaterThan($jamSelesai)) {
            $menit = $scan->diffInMinutes($jamMulai);
            return ['terlambat' => true, 'menit' => $menit];
        }

        return ['terlambat' => false, 'menit' => 0];
    }

    // Hitung keterlambatan pulang
    public static function hitungKeterlambatanKeluar($jamScan)
    {
        $pengaturan = PengaturanWaktu::getSekolahPulang();
        if (!$pengaturan) return ['terlambat' => false, 'menit' => 0];

        $jamMulai = Carbon::parse($pengaturan->jam_mulai);
        $jamSelesai = Carbon::parse($pengaturan->jam_selesai);
        $scan = Carbon::parse($jamScan);

        if ($scan->greaterThan($jamSelesai)) {
            $menit = $scan->diffInMinutes($jamMulai);
            return ['terlambat' => true, 'menit' => $menit];
        }

        return ['terlambat' => false, 'menit' => 0];
    }
}
