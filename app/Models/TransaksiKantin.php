<?php
// app/Models/TransaksiKantin.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiKantin extends Model
{
    protected $table = 'transaksi_kantin';
    
    protected $fillable = [
        'user_id', 'jenis', 'jumlah', 'saldo_sebelum', 'saldo_sesudah', 'keterangan'
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'saldo_sebelum' => 'decimal:2',
        'saldo_sesudah' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}