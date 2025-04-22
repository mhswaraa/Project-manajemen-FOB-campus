<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Investor;

class ProfileController extends Controller
{
    /**
     * Tampilkan profil / form create investor.
     */
    public function index()
    {
        $user       = Auth::user();
        // Ambil record investor terkait user
        $investor   = Investor::where('user_id', $user->id)->first();

        return view('investor.profile.index', compact('investor'));
    }

    /**
     * Simpan data diri (create atau update).
     */
    public function storeOrUpdate(Request $request)
    {
        $user = Auth::user();

        // validasi common
        $rules = [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'phone'    => 'required|string|max:20',
            'amount'   => 'required|numeric|min:0',
            'deadline' => 'required|date|after_or_equal:today',
          ];
          
        $data = $request->validate($rules);

        // Tambahkan user_id agar link ke tabel users
        $data['user_id'] = $user->id;

        // Create or Update
        Investor::updateOrCreate(
            ['user_id' => $user->id],   // cari berdasarkan user_id
            $data                        // data baru
        );

        return redirect()
            ->route('investor.profile')
            ->with('success','Data diri investor berhasil disimpan.');
    }
}
