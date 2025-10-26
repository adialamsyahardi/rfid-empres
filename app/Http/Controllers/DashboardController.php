<?php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PresensiSekolah;
use App\Models\PresensiSholat;
use App\Models\TransaksiKantin;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        $data = [
            'total_users' => User::where('role', 'user')->count(),
            'presensi_sekolah_hari_ini' => PresensiSekolah::whereDate('tanggal', $today)->count(),
            'presensi_sholat_hari_ini' => PresensiSholat::whereDate('tanggal', $today)->count(),
            'transaksi_hari_ini' => TransaksiKantin::whereDate('created_at', $today)->count(),
            'total_saldo_sistem' => User::sum('saldo'),
        ];

        // Presensi terkini
        $data['presensi_terkini'] = PresensiSekolah::with('user')
            ->whereDate('tanggal', $today)
            ->latest()
            ->take(10)
            ->get();

        // Transaksi terkini
        $data['transaksi_terkini'] = TransaksiKantin::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', $data);
    }
}
