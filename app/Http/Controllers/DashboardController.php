<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Project;

class DashboardController extends Controller
{
    public function index()
    {
        // Semua role dibutuhkan data yang berbeda:
        switch (Auth::user()->role) {
            case 'admin':
                // Hitung statistik & list
                $adminCount    = User::where('role','admin')->count();
                $investorCount = User::where('role','investor')->count();
                $ownerCount    = User::where('role','ceo')->count();
                $penjahitCount = User::where('role','penjahit')->count();
                $projectCount  = Project::count();
                $investors     = User::where('role','investor')->get(['id','name','email']);
                $penjahits     = User::where('role','penjahit')->get(['id','name','email']);

                return view('dashboard.admin', compact(
                    'adminCount','investorCount','ownerCount','penjahitCount','projectCount',
                    'investors','penjahits'
                ));

            case 'ceo':
                return view('dashboard.ceo');
            case 'investor':
                return view('dashboard.investor');
            case 'penjahit':
                return view('dashboard.penjahit');
            default:
                abort(403);
        }
    }
}
