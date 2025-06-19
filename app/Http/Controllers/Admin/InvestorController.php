<?php
// Path: app/Http/Controllers/Admin/InvestorController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\Investor;

class InvestorController extends Controller
{
    public function index()
    {
        $investorCount = Investor::count();
        $investors     = Investor::latest()->get();
        return view('admin.investors.index', compact('investorCount','investors'));
    }

    public function create()
    {
        return view('admin.investors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:investors,email',
            'phone'    => 'required|string|max:20',
            'amount'   => 'required|numeric|min:0',
            'deadline' => 'required|date|after_or_equal:today',
        ]);

        // 📌 Tambahkan baris ini:
        $data['user_id'] = Auth::id();

        Investor::create($data);

        return redirect()->route('admin.investors.index')
                         ->with('success','Investor telah berhasil ditambahkan.');
    }

    public function edit(Investor $investor)
    {
        return view('admin.investors.edit', compact('investor'));
    }

    public function update(Request $request, Investor $investor)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:investors,email,'.$investor->investor_id.',investor_id',
            'phone'    => 'required|string|max:20',
            'amount'   => 'required|numeric|min:0',
            'deadline' => 'required|date|after_or_equal:today',
        ]);

        $investor->update($data);

        return redirect()->route('admin.investors.index')
                         ->with('success','Investor telah berhasil di‑update.');
    }

    public function destroy(Investor $investor)
    {
        $investor->delete();

        return redirect()->route('admin.investors.index')
                         ->with('success','Investor telah berhasil di‑hapus.');
    }
}
