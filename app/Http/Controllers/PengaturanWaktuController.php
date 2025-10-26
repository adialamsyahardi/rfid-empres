<?php
// app/Http/Controllers/PengaturanWaktuController.php
namespace App\Http\Controllers;

use App\Models\PengaturanWaktu;
use Illuminate\Http\Request;

class PengaturanWaktuController extends Controller
{
    public function index()
    {
        $pengaturan = PengaturanWaktu::orderBy('jenis')->get();
        return view('pengaturan.waktu', compact('pengaturan'));
    }

    public function update(Request $request, PengaturanWaktu $pengaturan)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'toleransi_keterlambatan' => 'required|integer|min:0',
            'aktif' => 'required|boolean'
        ]);

        $pengaturan->update([
            'nama' => $request->nama,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'toleransi_keterlambatan' => $request->toleransi_keterlambatan,
            'aktif' => $request->aktif
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan waktu berhasil diperbarui!'
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:sekolah,sholat,kustom',
            'nama' => 'required|string|max:255',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'toleransi_keterlambatan' => 'required|integer|min:0'
        ]);

        $pengaturan = PengaturanWaktu::create([
            'jenis' => $request->jenis,
            'nama' => $request->nama,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'toleransi_keterlambatan' => $request->toleransi_keterlambatan,
            'aktif' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan waktu berhasil ditambahkan!',
            'data' => $pengaturan
        ]);
    }

    public function destroy(PengaturanWaktu $pengaturan)
    {
        $pengaturan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan waktu berhasil dihapus!'
        ]);
    }
}
