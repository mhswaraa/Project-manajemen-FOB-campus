<?php
// Path: app/Http/Controllers/Admin/DashboardController.php
// Compare this snippet from app/Http/Controllers/Admin/DashboardController.php:
// <?php    

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Project;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // hitung data
        $adminCount     = User::where('role','admin')->count();
        $investorCount  = User::where('role','investor')->count();
        $ownerCount     = User::where('role','ceo')->count();
        $penjahitCount  = User::where('role','penjahit')->count();
        $projectCount   = Project::count();

        // ambil list investor & penjahit
        $investors = User::where('role','investor')->get(['id','name','email']);
        $penjahits = User::where('role','penjahit')->get(['id','name','email']);

        // kirim ke view
        return view('dashboard.admin', compact(
            'adminCount','investorCount','ownerCount','penjahitCount','projectCount',
            'investors','penjahits'
        ));
    }
}
