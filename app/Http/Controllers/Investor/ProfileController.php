<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Investor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // PERBAIKAN: Menambahkan import untuk Storage

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil investor.
     */
    public function index()
    {
        $user = Auth::user();
        $investor = $user->investor;

        // Data untuk kartu ringkasan portofolio
        $totalInvested = 0;
        $projectsCount = 0;
        $estimatedProfit = 0;

        if ($investor) {
            $totalInvested = $investor->investments()->where('approved', true)->sum('amount');
            $projectsCount = $investor->investments()->where('approved', true)->distinct('project_id')->count();
            
            // Kalkulasi estimasi profit
            $estimatedProfit = DB::table('investments')
                ->join('projects', 'investments.project_id', '=', 'projects.id')
                ->where('investments.investor_id', $investor->id) // Menggunakan $investor->id
                ->where('investments.approved', true)
                ->sum(DB::raw('investments.qty * projects.profit'));
        }
        
        return view('investor.profile.index', compact(
            'user', 
            'investor',
            'totalInvested',
            'projectsCount',
            'estimatedProfit'
        )); 
    }

    public function downloadMOU()
    {
        $investor = Auth::user();
        $pdf = Pdf::loadView('investor.profile.mou_pdf', compact('investor'));

        return $pdf->download('MOU-Investasi-' . $investor->name . '.pdf');
    }

    /**
     * Upload the signed MOU.
     */
    public function uploadMOU(Request $request)
    {
        $request->validate([
            'mou_document' => 'required|file|mimes:pdf|max:5120', // Maksimal 5MB
        ]);

        $investor = Auth::user()->investor;

        // Hapus file MOU lama jika ada
        if ($investor->mou_path && Storage::disk('public')->exists($investor->mou_path)) {
            Storage::disk('public')->delete($investor->mou_path);
        }

        // Simpan file baru
        $path = $request->file('mou_document')->store('mou_investor', 'public');
        $investor->mou_path = $path;
        $investor->save(); // Ini akan berjalan dengan benar setelah 'Storage' di-import

        return back()->with('success', 'Dokumen MOU berhasil diunggah.');
    }

    /**
     * Menyimpan atau memperbarui profil investor.
     */
    public function storeOrUpdate(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $user->update(['name' => $request->name]);

        Investor::updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone'         => $request->phone,
                'name'          => $request->name,
                'email'         => $user->email,
                'registered_at' => now(),
            ]
        );

        // PERBAIKAN: Ubah nama rute menjadi 'investor.profile.index'
        return redirect()->route('investor.profile.index')
                         ->with('success', 'Profil Anda telah berhasil diperbarui.');
    }
}
