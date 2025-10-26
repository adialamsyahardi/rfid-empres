<?php
// app/Models/PenggunaanHarian.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenggunaanHarian extends Model
{
    protected $table = 'penggunaan_harian';
    
    protected $fillable = [
        'user_id', 'tanggal', 'total_pengeluaran'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_pengeluaran' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}