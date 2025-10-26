<?php
// app/Models/JadwalSholat.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalSholat extends Model
{
    protected $table = 'jadwal_sholat';
    
    protected $fillable = [
        'tanggal', 'subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
