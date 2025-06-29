<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule; // <-- TAMBAHKAN BARIS INI

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil investor.
     */
     public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $investor = Investor::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name'  => $user->name,
                'email' => $user->email,
                // =================== KEMBALIKAN KE 'phone' ===================
                'phone' => '' 
            ]
        );

        $totalInvested = $investor->investments()->where('approved', true)->sum('amount');
        $projectsCount = $investor->investments()->where('approved', true)->distinct('project_id')->count();
        
        $estimatedProfit = DB::table('investments')
            ->join('projects', 'investments.project_id', '=', 'projects.id')
            ->where('investments.investor_id', $investor->id)
            ->where('investments.approved', true)
            ->sum(DB::raw('investments.qty * projects.profit'));
        
        return view('investor.profile.index', compact(
            'user', 
            'investor',
            'totalInvested',
            'projectsCount',
            'estimatedProfit'
        )); 
    }

    /**
     * Mengunduh dokumen MOU.
     */
    public function downloadMOU()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $investor = $user->investor;

        if (!$investor || !$investor->nik || !$investor->alamat) {
            return redirect()->route('investor.profile.index')
                             ->with('error', 'Harap lengkapi NIK dan Alamat pada profil Anda sebelum mengunduh MOU.');
        }

        $pdf = Pdf::loadView('investor.profile.mou_pdf', [
            'user' => $user,
            'investor' => $investor
        ]);

        return $pdf->download('MOU-Investasi-' . $user->name . '.pdf');
    }

    /**
     * Mengunggah dokumen MOU yang telah ditandatangani.
     */
    public function uploadMOU(Request $request)
    {
        $request->validate([
            'mou_document' => 'required|file|mimes:pdf|max:5120', // Maksimal 5MB
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $investor = $user->investor;

        // Hapus file MOU lama jika ada untuk menghindari penumpukan file
        if ($investor->mou_path && Storage::disk('public')->exists($investor->mou_path)) {
            Storage::disk('public')->delete($investor->mou_path);
        }

        // Simpan file baru di storage/app/public/mou_investor
        $path = $request->file('mou_document')->store('mou_investor', 'public');
        
        $investor->mou_path = $path;
        $investor->save();

        return back()->with('success', 'Dokumen MOU berhasil diunggah.');
    }

    /**
     * Menyimpan atau memperbarui profil investor.
     */
      public function storeOrUpdate(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $investor = $user->investor; 

        $nikRule = ['required', 'string', 'size:16'];
        if ($investor) {
            $nikRule[] = Rule::unique('investors')->ignore($investor->id);
        } else {
            $nikRule[] = Rule::unique('investors');
        }

        // =================== KEMBALIKAN KE 'phone' ===================
        $request->validate([
            'name'   => 'required|string|max:255',
            'phone'  => 'required|string|max:20', // DIUBAH
            'nik'    => $nikRule,
            'alamat' => 'required|string|max:1000',
        ]);

        $user->update(['name' => $request->name]);

        Investor::updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone'  => $request->phone, // DIUBAH
                'nik'    => $request->nik,
                'alamat' => $request->alamat,
                'name'   => $request->name,
                'email'  => $user->email,
            ]
        );

        return redirect()->route('investor.profile')
                         ->with('success', 'Profil Anda telah berhasil diperbarui.');
    }
}
