<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Investor;
use App\Models\Tailor;
use Illuminate\Http\Request; // <-- Import Request
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua user dengan filter berdasarkan role.
     */
    public function index(Request $request)
    {
        $role = $request->query('role');

        // Query dasar untuk mengambil user
        $usersQuery = User::latest();

        // Terapkan filter jika ada role yang dipilih
        if ($role && in_array($role, ['admin', 'ceo', 'investor', 'penjahit'])) {
            $usersQuery->where('role', $role);
        }

        $users = $usersQuery->paginate(15)->withQueryString();

        // Ambil jumlah total untuk setiap role untuk ditampilkan di tab
        $roleCounts = User::query()
            ->select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        // Siapkan data jumlah untuk view, pastikan semua role ada
        $counts = [
            'all' => User::count(),
            'admin' => $roleCounts->get('admin', 0),
            'ceo' => $roleCounts->get('ceo', 0),
            'investor' => $roleCounts->get('investor', 0),
            'penjahit' => $roleCounts->get('penjahit', 0),
        ];

        return view('admin.users.index', [
            'users' => $users,
            'counts' => $counts,
            'currentRole' => $role, // Untuk menandai tab mana yang aktif
        ]);
    }

    // ... sisa method (create, store, edit, update, destroy) tidak berubah ...
    // ... (kode Anda yang sudah ada di sini) ...

    /**
     * Menampilkan form untuk membuat user baru.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Menyimpan user baru ke database.
     */
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,ceo,investor,penjahit'],
            'gdrive_link' => ['nullable', 'url'], // <-- Validasi baru
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'email_verified_at' => now(),
            ]);

            if ($request->role === 'investor') {
                Investor::create([
                    'user_id' => $user->id,
                    'gdrive_link' => $request->gdrive_link, // <-- Simpan link
                ]);
            } elseif ($request->role === 'penjahit') {
                Tailor::create([
                    'user_id' => $user->id,
                    'gdrive_link' => $request->gdrive_link, // <-- Simpan link
                ]);
            }
        });

        return redirect()->route('admin.users.index')->with('success', 'User baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit user.
     */
    public function edit(User $user)
    {
        // Eager load relasi untuk mendapatkan gdrive_link
        $user->load('investor', 'tailor');
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Memperbarui data user di database.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id],
            'role' => ['required', 'in:admin,ceo,investor,penjahit'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'gdrive_link' => ['nullable', 'url'], // <-- Validasi baru
        ]);
        
        DB::transaction(function () use ($request, $user) {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
            ]);

            if ($user->role === 'investor' && $user->investor) {
                $user->investor->update(['gdrive_link' => $request->gdrive_link]);
            } elseif ($user->role === 'penjahit' && $user->tailor) {
                $user->tailor->update(['gdrive_link' => $request->gdrive_link]);
            }
        });

        return redirect()->route('admin.users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    /**
     * Menghapus user dari database.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        DB::transaction(function () use ($user) {
            if ($user->investor) {
                $user->investor->delete();
            }
            if ($user->tailor) {
                $user->tailor->delete();
            }
            $user->delete();
        });

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
