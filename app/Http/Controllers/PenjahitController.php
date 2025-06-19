<?php

// Path: app/Http/Controllers/PenjahitController.php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Tailor;

class PenjahitController extends Controller
{
    public function index()
    {
        return view('dashboard.penjahit'); // pastikan view ini ada: resources/views/dashboards/tailor.blade.php
    }
    public function create()
{
    $user = Auth::user();
    return view('penjahit.create', compact('user'));
}

public function store(Request $req)
{
    $data = $req->validate([
        'phone'    => 'required',
        'amount'   => 'required|numeric',
        'deadline' => 'required|date',
    ]);
    $data['user_id'] = Auth::id();
    $data['name']    = Auth::user()->name;
    $data['email']   = Auth::user()->email;

    Tailor::create($data);
    return redirect()->route('dashboard')->with('success','Data investor tersimpan.');
}
}