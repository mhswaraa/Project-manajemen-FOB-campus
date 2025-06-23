<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Investment;
use App\Notifications\InvestmentApprovedNotification; // <-- INI ADALAH PERBAIKANNYA

class ProjectController extends Controller
{
    /**
     * Menampilkan daftar proyek yang tersedia untuk investasi.
     */
    public function index(Request $request)
    {
        // 1. Mulai query untuk proyek yang aktif
        $query = Project::where('status', 'active');

        // 2. Eager-load data agregat untuk performa
        $query->withSum(['investments as funded_qty' => function($q) {
            $q->where('approved', true);
        }], 'qty');

        // 3. Terapkan filter pencarian jika ada
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 4. Terapkan pengurutan
        $sortBy = $request->input('sort_by', 'latest');
        if ($sortBy == 'deadline') {
            $query->orderBy('deadline', 'asc');
        } elseif ($sortBy == 'popular') {
            $query->orderBy('funded_qty', 'desc');
        } else {
            $query->latest();
        }

        // 5. Ambil semua proyek yang memenuhi kriteria
        $projects = $query->get()
            ->filter(function ($project) {
                $funded = $project->funded_qty ?? 0;
                return $funded < $project->quantity;
            });
        
        return view('investor.projects.index', [
            'projects' => $projects,
            'search' => $request->search,
            'sortBy' => $sortBy,
        ]);
    }

    /**
     * Menampilkan form untuk melakukan investasi pada sebuah proyek.
     */
    public function create(Project $project)
    {
        $project->loadSum(['investments as funded_qty' => function ($q) {
            $q->where('approved', true);
        }], 'qty');
        
        $funded = $project->funded_qty ?? 0;
        $remainingQty = $project->quantity - $funded;

        return view('investor.projects.invest', compact('project', 'remainingQty'));
    }

    /**
     * Menyimpan investasi baru.
     */
    public function store(Request $request, Project $project)
    {
        $investor = $request->user()->investor;
        
        $request->validate([
            'qty'       => 'required|integer|min:1',
            'receipt'   => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'agreement' => 'accepted',
        ]);
        
        $funded = $project->investments()->where('approved', true)->sum('qty');
        $remainingQty = $project->quantity - $funded;

        if ($request->qty > $remainingQty) {
            return back()->withErrors(['qty' => 'Jumlah investasi melebihi slot yang tersedia.'])->withInput();
        }
        
        $receiptPath = $request->file('receipt')->store('investment_receipts', 'public');
        
        Investment::create([
            'project_id'  => $project->id,
            'investor_id' => $investor->investor_id,
            'qty'         => $request->qty,
            'amount'      => $request->qty * $project->price_per_piece,
            'receipt'     => $receiptPath,
            'message'     => $request->message,
            'approved'    => false,
        ]);

        return redirect()->route('investor.investments.index')
                         ->with('success', 'Investasi Anda telah diajukan dan sedang menunggu persetujuan Admin.');
    }
}
