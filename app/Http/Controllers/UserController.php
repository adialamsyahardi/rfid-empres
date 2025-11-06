<?php
// app/Http/Controllers/UserController.php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users with search, filter, and sorting
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search by name, email, or RFID card
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('rfid_card', 'like', "%{$search}%");
            });
        }

        // Filter by role (admin/user)
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Sorting
        switch ($request->get('sort', 'name_asc')) {
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'saldo_desc':
                $query->orderBy('saldo', 'desc');
                break;
            case 'saldo_asc':
                $query->orderBy('saldo', 'asc');
                break;
            case 'created_desc':
                $query->orderBy('created_at', 'desc');
                break;
            default: // name_asc
                $query->orderBy('name', 'asc');
                break;
        }

        // Paginate with query string preservation
        $users = $query->paginate(10)->withQueryString();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user in storage
     */
/**
 * Store a newly created user in storage
 */
public function store(Request $request)
{
    $request->validate([
        'rfid_card' => 'required|string|unique:users,rfid_card',
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6',
        'role' => 'required|in:admin,user',
        'tempat_lahir' => 'nullable|string|max:100',
        'tanggal_lahir' => 'nullable|date|before:today',
        'jenis_kelamin' => 'nullable|in:L,P',
        'alamat' => 'nullable|string|max:500',
        'limit_harian' => 'nullable|numeric|min:0|max:1000000',
        'saldo' => 'nullable|numeric|min:0|max:10000000'
    ], [
        'rfid_card.required' => 'RFID Card wajib diisi',
        'rfid_card.unique' => 'RFID Card sudah terdaftar',
        'name.required' => 'Nama wajib diisi',
        'email.required' => 'Email wajib diisi',
        'email.unique' => 'Email sudah terdaftar',
        'password.required' => 'Password wajib diisi',
        'password.min' => 'Password minimal 6 karakter',
        'role.required' => 'Role wajib dipilih',
        'tanggal_lahir.before' => 'Tanggal lahir tidak valid',
        'jenis_kelamin.in' => 'Jenis kelamin harus L atau P',
        'limit_harian.max' => 'Limit harian maksimal Rp 1.000.000',
        'saldo.max' => 'Saldo maksimal Rp 10.000.000'
    ]);

    try {
        $user = User::create([
            'rfid_card' => $request->rfid_card,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'limit_harian' => $request->limit_harian ?? 10000,
            'saldo' => $request->saldo ?? 0,
            // ✅ FIX: Kirim 1 atau 0 (integer)
            'limit_saldo_aktif' => $request->has('limit_saldo_aktif') ? 1 : 0,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', "✅ User {$user->name} berhasil ditambahkan!");

    } catch (\Exception $e) {
        \Log::error('Error creating user', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()
            ->back()
            ->withInput()
            ->with('error', '❌ Gagal menambahkan user: ' . $e->getMessage());
    }
}

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage
     */
/**
 * Update the specified user in storage
 */
public function update(Request $request, User $user)
{
    $request->validate([
        'rfid_card' => 'required|string|unique:users,rfid_card,' . $user->id,
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|string|min:6',
        'role' => 'required|in:admin,user',
        'tempat_lahir' => 'nullable|string|max:100',
        'tanggal_lahir' => 'nullable|date|before:today',
        'jenis_kelamin' => 'nullable|in:L,P',
        'alamat' => 'nullable|string|max:500',
        'limit_harian' => 'nullable|numeric|min:0|max:1000000',
        // ✅ HAPUS VALIDASI SALDO
    ], [
        'rfid_card.required' => 'RFID Card wajib diisi',
        'rfid_card.unique' => 'RFID Card sudah digunakan user lain',
        'name.required' => 'Nama wajib diisi',
        'email.required' => 'Email wajib diisi',
        'email.unique' => 'Email sudah digunakan user lain',
        'password.min' => 'Password minimal 6 karakter',
        'role.required' => 'Role wajib dipilih',
        'tanggal_lahir.before' => 'Tanggal lahir tidak valid',
        'jenis_kelamin.in' => 'Jenis kelamin harus L atau P',
        'limit_harian.max' => 'Limit harian maksimal Rp 1.000.000',
        // ✅ HAPUS MESSAGE SALDO
    ]);

    try {
        // ✅ Prepare update data (TANPA SALDO)
        $data = [
            'rfid_card' => $request->rfid_card,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'limit_harian' => $request->limit_harian ?? 10000,
            // ✅ HAPUS SALDO DARI DATA UPDATE
            'limit_saldo_aktif' => $request->has('limit_saldo_aktif') ? 1 : 0,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Debug log (optional, bisa dihapus nanti)
        \Log::info('Updating user', [
            'user_id' => $user->id,
            'old_data' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'tempat_lahir' => $user->tempat_lahir,
                'limit_harian' => $user->limit_harian,
                'limit_saldo_aktif' => $user->limit_saldo_aktif,
                'saldo' => $user->saldo, // ✅ LOG SALDO (TAPI TIDAK DIUBAH)
            ],
            'new_data' => [
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'],
                'tempat_lahir' => $data['tempat_lahir'],
                'limit_harian' => $data['limit_harian'],
                'limit_saldo_aktif' => $data['limit_saldo_aktif'],
            ]
        ]);

        // ✅ Update user (SALDO TIDAK IKUT DIUPDATE)
        $user->update($data);

        // Refresh model to get updated data
        $user->refresh();

        return redirect()
            ->route('users.index')
            ->with('success', "✅ User {$user->name} berhasil diperbarui! (Saldo tidak berubah: Rp " . number_format($user->saldo, 0, ',', '.') . ")");

    } catch (\Exception $e) {
        \Log::error('Error updating user', [
            'user_id' => $user->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()
            ->back()
            ->withInput()
            ->with('error', '❌ Gagal memperbarui user: ' . $e->getMessage());
    }
}

    /**
     * Remove the specified user from storage
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deleting own account
            if ($user->id === auth()->id()) {
                return redirect()
                    ->route('users.index')
                    ->with('error', '❌ Tidak dapat menghapus akun Anda sendiri!');
            }

            // Prevent deleting the last admin
            if ($user->role === 'admin') {
                $adminCount = User::where('role', 'admin')->count();
                if ($adminCount <= 1) {
                    return redirect()
                        ->route('users.index')
                        ->with('error', '❌ Tidak dapat menghapus admin terakhir!');
                }
            }

            $userName = $user->name;
            $user->delete();

            return redirect()
                ->route('users.index')
                ->with('success', "✅ User {$userName} berhasil dihapus!");

        } catch (\Exception $e) {
            \Log::error('Error deleting user: ' . $e->getMessage());
            
            return redirect()
                ->route('users.index')
                ->with('error', '❌ Gagal menghapus user: ' . $e->getMessage());
        }
    }

    /**
     * Get user details by RFID (for AJAX)
     */
    public function getByRfid(Request $request)
    {
        $request->validate([
            'rfid_card' => 'required|string'
        ]);

        $user = User::where('rfid_card', $request->rfid_card)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rfid_card' => $user->rfid_card,
                'saldo' => $user->saldo,
                'limit_harian' => $user->limit_harian,
                'limit_saldo_aktif' => $user->limit_saldo_aktif
            ]
        ]);
    }

    /**
     * Bulk import users (optional feature for future)
     */
    public function import(Request $request)
    {
        // TODO: Implement bulk import from CSV/Excel
        return response()->json([
            'success' => false,
            'message' => 'Fitur import belum tersedia'
        ], 501);
    }

    /**
     * Export users to CSV (optional feature for future)
     */
    public function export(Request $request)
    {
        // TODO: Implement export to CSV
        return response()->json([
            'success' => false,
            'message' => 'Fitur export belum tersedia'
        ], 501);
    }
}