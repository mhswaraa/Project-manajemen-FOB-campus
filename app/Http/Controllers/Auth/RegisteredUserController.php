<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Investor;
use App\Models\Tailor;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password'    => ['required', 'confirmed', Rules\Password::defaults()],
            'role'        => ['required', 'in:investor,penjahit'],
            'gdrive_link' => ['nullable', 'string', 'url', 'max:255'],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        if ($user->role === 'investor') {
            Investor::create([
                'user_id'     => $user->id,
                'name'        => $request->name,
                'email'       => $request->email,
                'gdrive_link' => $request->gdrive_link,
                'phone'       => '', // Dibiarkan kosong agar user wajib melengkapi
            ]);
        } elseif ($user->role === 'penjahit') {
            Tailor::create([
                'user_id'     => $user->id,
                'name'        => $request->name,
                'email'       => $request->email,
                'gdrive_link' => $request->gdrive_link,
                'phone'       => '', // Dibiarkan kosong agar user wajib melengkapi
            ]);
        }

        event(new Registered($user));

        // Karena admin yang mendaftarkan, mungkin lebih baik redirect ke halaman daftar user
        // daripada login otomatis. Jika ini halaman registrasi publik, baris di bawah ini benar.
        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
