<?php
// Path: app/Http/Controllers/Penjahit/ProfileController.php
namespace App\Http\Controllers\Penjahit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Tailor;

class ProfileController extends Controller
{
    /**
     * Tampilkan form profil / lengkapi data diri penjahit.
     */
    public function index()
    {
        $user   = Auth::user();
        $tailor = $user->tailor; 

        return view('penjahit.profile.index', compact('user','tailor'));
    }

    /**
     * Simpan (create/update) data profil penjahit.
     */
    public function storeOrUpdate(Request $request)
    {
        /** @var User $user */ 
        $user = Auth::user();

        // Validasi input
        $rules = [
            'address'  => 'required|string|max:255',
            'phone'    => 'required|string|max:20',
            'email'    => 'required|email|max:255|unique:penjahits,email,'
                          . optional($user->tailor)->tailor_id . ',tailor_id',
            'status'   => 'required|in:available,busy,inactive',
            'password' => 'nullable|confirmed|min:8',
        ];
        $data = $request->validate($rules);

        // Jika password diisi, update password di tabel users
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        // Siapkan data untuk tabel penjahits
        $tailorData = [
            'user_id' => $user->id,
            'address' => $data['address'],
            'phone'   => $data['phone'],
            'email'   => $data['email'],
            'status'  => $data['status'],
        ];

        // Buat atau update record Tailor
        Tailor::updateOrCreate(
            ['user_id' => $user->id],
            $tailorData
        );

        return redirect()
            ->route('penjahit.profile')
            ->with('success', 'Profil penjahit berhasil disimpan.');
    }
}
