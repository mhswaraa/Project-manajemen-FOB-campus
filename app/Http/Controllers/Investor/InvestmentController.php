<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Investment;
use Illuminate\Support\Facades\Storage;

class InvestmentController extends Controller
{
    /**
     * Menampilkan daftar investasi milik investor yang sedang login.
     */
    public function index(Request $request)
    {
        $investor = $request->user()->investor;
        $tab = $request->query('tab', 'all');

        $query = $investor->investments()
                          ->with([
                              'project',
                              'project.progress', // Eager load progress untuk kalkulasi
                              'project.investments' // Eager load investments untuk total pendanaan
                          ])
                          ->latest();

        if ($tab === 'pending') {
            $query->where('approved', false);
        } elseif ($tab === 'active') {
            $query->where('approved', true);
        }
        
        $investments = $query->paginate(9)->withQueryString();

        // ====================================================================
        // AWAL PERUBAHAN: Kalkulasi progres produksi disesuaikan
        // ====================================================================
        $investments->getCollection()->transform(function ($investment) {
            $project = $investment->project;
            if ($project) {
                // Total dana (dalam pcs) yang sudah terkumpul dan disetujui untuk proyek ini
                $totalFundedQty = $project->investments->where('approved', true)->sum('qty');
                
                // Total produksi yang sudah selesai dan DITERIMA oleh QC
                $totalCompletedAccepted = $project->progress->where('status', 'approved')->sum('accepted_qty');
                
                // Hitung persentase progres produksi
                $investment->production_progress = $totalFundedQty > 0 
                    ? round(($totalCompletedAccepted / $totalFundedQty) * 100) 
                    : 0;

                // Lampirkan data mentah untuk ditampilkan di view
                $investment->production_completed_qty = $totalCompletedAccepted;
                $investment->production_target_qty = $totalFundedQty;

            } else {
                $investment->production_progress = 0;
                $investment->production_completed_qty = 0;
                $investment->production_target_qty = 0;
            }
            return $investment;
        });
        // ====================================================================
        // AKHIR PERUBAHAN
        // ====================================================================

        return view('investor.investments.index', compact('investments', 'tab'));
    }

    /**
     * Menampilkan detail satu investasi.
     */
    public function show(Investment $investment)
    {
        if ($investment->investor_id !== auth()->user()->investor->investor_id) {
            abort(403);
        }

        $investment->load(['project.investments.investor.user', 'project.progress']);
        $project = $investment->project;

        // Hitung progres pendanaan proyek keseluruhan
        $totalFundedQty = $project->investments->where('approved', true)->sum('qty');
        $fundingPercentage = $project->quantity > 0 ? round(($totalFundedQty / $project->quantity) * 100) : 0;
        
        // ====================================================================
        // AWAL PERUBAHAN: Kalkulasi progres produksi disesuaikan
        // ====================================================================
        $totalCompletedAccepted = $project->progress->where('status', 'approved')->sum('accepted_qty');
        $productionPercentage = $totalFundedQty > 0 ? round(($totalCompletedAccepted / $totalFundedQty) * 100) : 0;
        // ====================================================================
        // AKHIR PERUBAHAN
        // ====================================================================

        return view('investor.investments.show', compact(
            'investment',
            'project',
            'fundingPercentage',
            'productionPercentage',
            'totalCompletedAccepted', // Kirim data mentah ke view
            'totalFundedQty'        // Kirim data mentah ke view
        ));
    }

    /**
     * Menampilkan form untuk mengedit investasi (jika belum diapprove).
     */
    public function edit(Investment $investment)
    {
        if ($investment->investor_id !== auth()->user()->investor->investor_id || $investment->approved) {
            abort(403);
        }
        return view('investor.investments.edit', compact('investment'));
    }

    /**
     * Mengupdate data investasi.
     */
    public function update(Request $request, Investment $investment)
    {
        if ($investment->investor_id !== auth()->user()->investor->investor_id || $investment->approved) {
            abort(403);
        }
        // Logika untuk update investasi bisa ditambahkan di sini jika diperlukan
        // Contoh: $investment->update($request->all());

        return redirect()->route('investor.investments.show', $investment)->with('success', 'Investasi berhasil diperbarui.');
    }
    
    /**
     * Menghapus/membatalkan pengajuan investasi.
     */
    public function destroy(Investment $investment)
    {
        if ($investment->investor_id !== auth()->user()->investor->investor_id || $investment->approved) {
            abort(403);
        }
        
        // Hapus file receipt dari storage
        if ($investment->receipt) {
            Storage::disk('public')->delete($investment->receipt);
        }
        
        $investment->delete();

        return redirect()->route('investor.investments.index')
                         ->with('success', 'Pengajuan investasi telah berhasil dibatalkan.');
    }
}
