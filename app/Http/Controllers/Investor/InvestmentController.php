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
        $tab = $request->query('tab', 'all'); // Default ke tab 'all'

        // Query dasar untuk investasi milik investor ini
        $query = $investor->investments()
                        ->with([
                            'project', // Eager load project details
                            'project.progress', // Eager load progress produksi proyek
                            'project.assignments' // Eager load data penugasan penjahit
                        ])
                        ->latest();

        // Terapkan filter berdasarkan tab
        if ($tab === 'pending') {
            $query->where('approved', false);
        } elseif ($tab === 'active') {
            $query->where('approved', true);
        }
        
        $investments = $query->paginate(9)->withQueryString();

        // Hitung persentase progres produksi untuk setiap proyek
        $investments->getCollection()->transform(function ($investment) {
            $project = $investment->project;
            if ($project) {
                $totalAssigned = $project->assignments->sum('assigned_qty');
                $totalCompleted = $project->progress->sum('quantity_done');
                
                $investment->production_progress = $totalAssigned > 0 
                    ? round(($totalCompleted / $totalAssigned) * 100) 
                    : 0;
            } else {
                $investment->production_progress = 0;
            }
            return $investment;
        });

        return view('investor.investments.index', compact('investments', 'tab'));
    }

    /**
     * Menampilkan detail satu investasi.
     */
    public function show(Investment $investment)
    {
        // Pastikan investor hanya bisa melihat investasinya sendiri
        if ($investment->investor_id !== auth()->user()->investor->investor_id) {
            abort(403);
        }

        // Eager load data project dan relasi lainnya yang dibutuhkan
        $investment->load(['project.investments', 'project.progress', 'project.assignments', 'project.investments.investor.user']);
        $project = $investment->project;

        // Hitung progres pendanaan proyek keseluruhan
        $totalFundedQty = $project->investments->where('approved', true)->sum('qty');
        $fundingPercentage = $project->quantity > 0 ? round(($totalFundedQty / $project->quantity) * 100) : 0;
        
        // Hitung progres produksi proyek keseluruhan
        $totalAssigned = $project->assignments->sum('assigned_qty');
        $totalCompleted = $project->progress->sum('quantity_done');
        $productionPercentage = $totalAssigned > 0 ? round(($totalCompleted / $totalAssigned) * 100) : 0;

        return view('investor.investments.show', compact(
            'investment',
            'project',
            'fundingPercentage',
            'productionPercentage'
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
