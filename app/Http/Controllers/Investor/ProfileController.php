<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;       // pastikan import model User
use App\Models\Investor;

class ProfileController extends Controller
{
    /**
     * Tampilkan profil / form lengkapi data.
     */
    public function index()
    {
        $user     = Auth::user();
        $investor = Investor::where('user_id', $user->id)->first();

        return view('investor.profile.index', compact('user','investor'));
    }

    /**
     * Simpan (create/update) data profil investor.
     */
    public function storeOrUpdate(Request $request)
    {
        // Doc-block agar Intelephense tahu $user adalah App\Models\User
        /** @var User $user */
        $user = Auth::user();

        // Validasi input
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|max:255|unique:users,email,'.$user->id,
            'phone'                 => 'required|string|max:20',
            'password'              => 'nullable|confirmed|min:8',
        ]);

        // Update User
        $user->update([
            'name'     => $data['name'],
            'email'    => $data['email'],
            // hanya hash jika password baru diisi
            'password' => $data['password']
                          ? Hash::make($data['password'])
                          : $user->password,
        ]);

        // Siapkan data investor
        $invData = [
            'user_id' => $user->id,
            'name'    => $data['name'],
            'email'   => $data['email'],
            'phone'   => $data['phone'],
        ];

        // Create atau update record di tabel investors
        Investor::updateOrCreate(
            ['user_id' => $user->id],   // key utk mencari existing
            $invData                    // data baru / updated
        );

        return redirect()
            ->route('investor.profile')
            ->with('success','Profil berhasil disimpan.');
    }
}
