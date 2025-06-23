<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Investor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class InvestorController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar untuk mengambil user dengan role investor
        $query = User::where('role', 'investor')
                    ->with(['investor']) // Eager load profil investor
                    ->withCount('investments') // Hitung jumlah investasi
                    ->withSum('investments', 'amount'); // Jumlahkan total nominal investasi

        // Terapkan pencarian jika ada
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        $investors = $query->latest()->paginate(10);
        
        // Data untuk kartu statistik
        $investorCount = User::where('role', 'investor')->count();
        $totalFunds = Investor::query()->withSum('investments', 'amount')->get()->sum('investments_sum_amount');
        
        return view('admin.investors.index', compact(
            'investors', 
            'investorCount',
            'totalFunds'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone'    => ['required', 'string', 'max:20'],
        ]);

        // 1. Buat record di tabel 'users'
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'investor', // Set role secara otomatis
        ]);
        
        // 2. Buat profil di tabel 'investors' yang terhubung dengan user baru
        $user->investor()->create([
            'phone' => $request->phone,
            'registered_at' => now(),
            // Kolom 'amount' dan 'deadline' yang lama tidak lagi relevan di sini
        ]);

        return redirect()->route('admin.investors.index')
                         ->with('success','Investor baru telah berhasil dibuat dan ditambahkan.');
    }

    public function edit(Investor $investor)
    {
        // Eager load relasi user untuk ditampilkan di form
        $investor->load('user');
        return view('admin.investors.edit', compact('investor'));
    }

    public function update(Request $request, Investor $investor)
    {
        $user = $investor->user;

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,'.$user->id,
            'phone'    => 'required|string|max:20',
        ]);
        
        // Update tabel user
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        
        // Update tabel investor
        $investor->update(['phone' => $request->phone]);

        return redirect()->route('admin.investors.index')
                         ->with('success','Data Investor telah berhasil diperbarui.');
    }

    public function destroy(Investor $investor)
    {
        // Hapus user akan otomatis menghapus investor profile karena relasi cascade
        $investor->user()->delete();

        return redirect()->route('admin.investors.index')
                         ->with('success','Investor telah berhasil dihapus.');
    }
}