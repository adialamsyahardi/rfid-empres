<?php
// app/Http/Controllers/KantinController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TransaksiKantin;
use App\Models\PenggunaanHarian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KantinController extends Controller
{
    public function cekSaldo()
    {
        return view('kantin.cek-saldo');
    }

    public function scanSaldo(Request $request)
    {
        $request->validate([
            'rfid_card' => 'required|string',
        ]);

        $user = User::where('rfid_card', $request->rfid_card)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu RFID tidak terdaftar!'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'saldo' => $user->saldo,
                'limit_aktif' => $user->limit_saldo_aktif,
                'limit_harian' => $user->limit_harian
            ]
        ]);
    }

    public function topup()
    {
        // ✅ TIDAK PERLU $users lagi karena pakai scan RFID
        return view('kantin.topup');
    }

    // ✅ METHOD BARU: Cari user by RFID untuk topup
    public function cariUserTopup(Request $request)
    {
        $request->validate([
            'rfid_card' => 'required|string',
        ]);

        $user = User::where('rfid_card', $request->rfid_card)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu RFID tidak terdaftar dalam sistem!'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'User ditemukan!',
            'data' => [
                'user' => $user,
                'id' => $user->id,
                'name' => $user->name,
                'rfid_card' => $user->rfid_card,
                'saldo' => $user->saldo,
                'limit_aktif' => $user->limit_saldo_aktif,
                'limit_harian' => $user->limit_harian
            ]
        ]);
    }

    public function prosesTopup(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'jumlah' => 'required|numeric|min:1000',
            'keterangan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $user = User::findOrFail($request->user_id);
            $saldoSebelum = $user->saldo;
            $user->saldo += $request->jumlah;
            $user->save();

            TransaksiKantin::create([
                'user_id' => $user->id,
                'jenis' => 'topup',
                'jumlah' => $request->jumlah,
                'saldo_sebelum' => $saldoSebelum,
                'saldo_sesudah' => $user->saldo,
                'keterangan' => $request->keterangan ?? 'Top up saldo'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Top up berhasil!',
                'data' => [
                    'nama' => $user->name,
                    'saldo_sebelum' => $saldoSebelum,
                    'saldo_baru' => $user->saldo,
                    'jumlah_topup' => $request->jumlah
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // ... method lainnya tetap sama ...
    
    public function bayar()
    {
        return view('kantin.bayar');
    }

    public function prosesBayar(Request $request)
    {
        $request->validate([
            'rfid_card' => 'required|string',
            'jumlah' => 'required|numeric|min:100',
        ]);

        DB::beginTransaction();
        try {
            $user = User::where('rfid_card', $request->rfid_card)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kartu RFID tidak terdaftar!'
                ], 404);
            }

            // Cek saldo mencukupi
            if ($user->saldo < $request->jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo tidak mencukupi! Saldo saat ini: Rp ' . number_format($user->saldo, 0, ',', '.')
                ], 400);
            }

            // Cek limit harian jika aktif
            if ($user->limit_saldo_aktif) {
                $today = Carbon::today();
                $penggunaan = PenggunaanHarian::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'tanggal' => $today
                    ],
                    ['total_pengeluaran' => 0]
                );

                $totalSetelahBayar = $penggunaan->total_pengeluaran + $request->jumlah;

                if ($totalSetelahBayar > $user->limit_harian) {
                    $sisaLimit = $user->limit_harian - $penggunaan->total_pengeluaran;
                    return response()->json([
                        'success' => false,
                        'message' => 'Melebihi limit harian! Limit: Rp ' . number_format($user->limit_harian, 0, ',', '.') . 
                                   '. Sudah digunakan: Rp ' . number_format($penggunaan->total_pengeluaran, 0, ',', '.') .
                                   '. Sisa limit: Rp ' . number_format($sisaLimit, 0, ',', '.')
                    ], 400);
                }

                // Update total pengeluaran harian
                $penggunaan->total_pengeluaran = $totalSetelahBayar;
                $penggunaan->save();
            }

            // Proses pembayaran
            $saldoSebelum = $user->saldo;
            $user->saldo -= $request->jumlah;
            $user->save();

            TransaksiKantin::create([
                'user_id' => $user->id,
                'jenis' => 'pembayaran',
                'jumlah' => $request->jumlah,
                'saldo_sebelum' => $saldoSebelum,
                'saldo_sesudah' => $user->saldo,
                'keterangan' => 'Pembayaran kantin'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil!',
                'data' => [
                    'nama' => $user->name,
                    'jumlah_bayar' => $request->jumlah,
                    'saldo_sebelum' => $saldoSebelum,
                    'saldo_sesudah' => $user->saldo
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function riwayat()
    {
        $transaksi = TransaksiKantin::with('user')
            ->latest()
            ->paginate(50);
        
        return view('kantin.riwayat', compact('transaksi'));
    }

    public function toggleLimit(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'status' => 'required|in:0,1,true,false'
            ]);

            $user = User::findOrFail($request->user_id);
            
            $status = filter_var($request->status, FILTER_VALIDATE_BOOLEAN);
            $user->limit_saldo_aktif = $status;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => $status 
                    ? '✅ Limit saldo berhasil diaktifkan!' 
                    : '✅ Limit saldo berhasil dinonaktifkan!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid: ' . $e->getMessage()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Toggle limit error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user_id,
                'status' => $request->status
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status limit'
            ], 500);
        }
    }
    public function getTransaksiUser(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id'
    ]);

    // Ambil 5 transaksi terakhir user ini
    $transaksi = TransaksiKantin::where('user_id', $request->user_id)
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    return response()->json([
        'success' => true,
        'data' => $transaksi->map(function($t) {
            return [
                'id' => $t->id,
                'jenis' => $t->jenis, // 'top_up' atau 'pembayaran'
                'jumlah' => $t->jumlah,
                'keterangan' => $t->keterangan,
                'waktu' => $t->created_at->format('d M Y H:i'),
                'waktu_relative' => $t->created_at->diffForHumans()
            ];
        })
    ]);
}
}