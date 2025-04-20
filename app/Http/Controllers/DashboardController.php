<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Project;
use App\Models\Investor;
use App\Models\Tailor;

class DashboardController extends Controller
{
    public function index()
    {
        $role = strtolower(Auth::user()->role);

        switch ($role) {
            case 'admin':
                // Statistik untuk cards
                $adminCount     = User::where('role','admin')->count();
                $investorCount  = User::where('role','investor')->count();
                $ownerCount     = User::where('role','ceo')->count();
                $penjahitCount  = User::where('role','penjahit')->count();
                $projectCount   = Project::count();

                // Data tabel
                $projects   = Project::all();
                $investors  = Investor::all();
                $penjahits  = Tailor::all();

                return view('dashboard.admin', compact(
                    'adminCount','investorCount','ownerCount','penjahitCount','projectCount',
                    'projects','investors','penjahits'
                ));

            case 'ceo':
                return view('dashboard.ceo');

            case 'investor':
                // (Optional) jika butuh data investor di dashboard
                $user = Auth::user()->investor; 
                return view('dashboard.investor', compact('user'));

            case 'penjahit':
                $user = Auth::user()->tailor;
                return view('dashboard.penjahit', compact('user'));

            default:
                abort(403, 'Unauthorized');
        }
    }
}
