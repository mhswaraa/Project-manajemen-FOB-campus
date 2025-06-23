<?php
// Path: app/Http/Controllers/Penjahit/ProfileController.php
namespace App\Http\Controllers\Penjahit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Tailor;
use App\Models\Portfolio;
use App\Models\Specialization;

class ProfileController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $tailor = $user->tailor()->with('portfolios', 'specializations')->first();
        $specializations = Specialization::all();

        return view('penjahit.profile.index', compact('user', 'tailor', 'specializations'));
    }

    public function storeOrUpdate(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $rules = [
            'name'     => 'required|string|max:255',
            'address'  => 'required|string|max:255',
            'phone'    => 'required|string|max:20',
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'status'   => 'required|in:available,busy,inactive',
            'specializations' => 'nullable|array',
            'specializations.*' => 'exists:specializations,id'
        ];
        $data = $request->validate($rules);

        // Update data di tabel users (nama dan email)
        $user->update(['name' => $data['name'], 'email' => $data['email']]);

        // Siapkan data untuk tabel penjahits
        $tailorData = [
            'user_id' => $user->id,
            'address' => $data['address'],
            'phone'   => $data['phone'],
            'status'  => $data['status'],
            'email'   => $data['email'],
        ];

        // Buat atau update record Tailor
        $tailor = Tailor::updateOrCreate(['user_id' => $user->id], $tailorData);

        // Simpan relasi spesialisasi
        if ($request->has('specializations')) {
            $tailor->specializations()->sync($request->specializations);
        } else {
            $tailor->specializations()->detach();
        }

        return redirect()->route('penjahit.profile.index')->with('success', 'Profil berhasil diperbarui.');
    }

    public function addPortfolio(Request $request)
    {
        $request->validate([
            'portfolio_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'portfolio_caption' => 'nullable|string|max:255',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $tailor = $user->tailor;
        
        if ($request->hasFile('portfolio_image')) {
            $path = $request->file('portfolio_image')->store('portfolios', 'public');

            $tailor->portfolios()->create([
                'image_path' => $path,
                'caption' => $request->portfolio_caption
            ]);
        }

        return back()->with('success', 'Gambar portofolio berhasil ditambahkan.');
    }

    public function deletePortfolio(Portfolio $portfolio)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($portfolio->tailor_id !== $user->tailor->tailor_id) {
            abort(403);
        }

        Storage::disk('public')->delete($portfolio->image_path);
        $portfolio->delete();

        return back()->with('success', 'Gambar portofolio berhasil dihapus.');
    }
}
