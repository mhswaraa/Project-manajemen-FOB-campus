<?php
// Path: app/Http/Controllers/InvestorController.php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Investor;

class InvestorController extends Controller
{
    public function index()
    {
        return view('dashboards.investor'); // pastikan view ini ada: resources/views/dashboards/investor.blade.php
    }
    public function create()
{
    $user = Auth::user();
    return view('investor.create', compact('user'));
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

    Investor::create($data);
    return redirect()->route('dashboard')->with('success','Data investor tersimpan.');
}
}