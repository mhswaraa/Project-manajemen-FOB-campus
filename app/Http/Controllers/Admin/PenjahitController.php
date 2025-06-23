<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tailor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class PenjahitController extends Controller
{
    public function index(Request $request)
    {
        $query = Tailor::with(['user', 'specializations'])
                        ->withCount('assignments')
                        ->withSum('progress', 'quantity_done');

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        $penjahits = $query->latest('tailor_id')->paginate(10);

        // Statistik untuk kartu
        $tailorCount = Tailor::count();
        $availableCount = Tailor::where('status', 'available')->count();
        $busyCount = Tailor::where('status', 'busy')->count();

        return view('admin.penjahits.index', compact(
            'penjahits',
            'tailorCount',
            'availableCount',
            'busyCount'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone'    => ['required', 'string', 'max:20'],
            'address'  => ['required', 'string', 'max:255'],
            'status'   => ['required', 'in:available,busy,inactive'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'penjahit',
        ]);

        $user->tailor()->create([
            'phone'   => $request->phone,
            'address' => $request->address,
            'status'  => $request->status,
            'email'   => $request->email, // Menyimpan email juga di profil untuk kemudahan
        ]);

        return redirect()->route('admin.penjahits.index')
                         ->with('success', 'Penjahit baru telah berhasil dibuat.');
    }

    public function edit(Tailor $penjahit)
    {
        $penjahit->load('user');
        return view('admin.penjahits.edit', compact('penjahit'));
    }

    public function update(Request $request, Tailor $penjahit)
    {
        $user = $penjahit->user;
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,'.$user->id,
            'phone'   => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'status'  => 'required|in:available,busy,inactive',
        ]);

        $user->update(['name' => $request->name, 'email' => $request->email]);
        $penjahit->update($request->only('phone', 'address', 'status', 'email'));

        return redirect()->route('admin.penjahits.index')
                         ->with('success', 'Data penjahit telah berhasil diperbarui.');
    }

    public function destroy(Tailor $penjahit)
    {
        // Menghapus User akan otomatis menghapus profil penjahit (cascade)
        $penjahit->user()->delete();

        return redirect()->route('admin.penjahits.index')
                         ->with('success', 'Penjahit telah berhasil dihapus.');
    }
}
