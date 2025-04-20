<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tailor; // model penjahit

class PenjahitController extends Controller
{
    public function index()
    {
        $tailorCount = Tailor::count();
        $tailors     = Tailor::latest()->get();
        return view('admin.penjahits.index', compact('tailorCount','tailors'));
    }

    public function create()
    {
        return view('admin.penjahits.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'address' => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'email'   => 'required|email|unique:penjahits,email',
            'status'  => 'required|in:available,busy,inactive',
        ]);

        // jika pakai relasi ke user
        $data['user_id'] = Auth::id();

        Tailor::create($data);

        return redirect()->route('admin.penjahits.index')
                         ->with('success','Penjahit telah berhasil ditambahkan.');
    }

    public function edit(Tailor $penjahit)
    {
        return view('admin.penjahits.edit', compact('penjahit'));
    }

    public function update(Request $request, Tailor $penjahit)
    {
        $data = $request->validate([
            'address' => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'email'   => 'required|email|unique:penjahits,email,'.$penjahit->tailor_id.',tailor_id',
            'status'  => 'required|in:available,busy,inactive',
        ]);

        $penjahit->update($data);

        return redirect()->route('admin.penjahits.index')
                         ->with('success','Penjahit telah berhasil di‑update.');
    }

    public function destroy(Tailor $penjahit)
    {
        $penjahit->delete();

        return redirect()->route('admin.penjahits.index')
                         ->with('success','Penjahit telah berhasil di‑hapus.');
    }
}
