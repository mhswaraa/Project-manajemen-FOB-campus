<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialization;
use Illuminate\Http\Request;

class SpecializationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $specializations = Specialization::latest()->paginate(15);
        return view('admin.specializations.index', compact('specializations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:specializations,name',
        ]);

        Specialization::create($request->all());

        return back()->with('success', 'Spesialisasi baru berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Specialization $specialization)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:specializations,name,' . $specialization->id,
        ]);

        $specialization->update($request->all());

        return redirect()->route('admin.specializations.index')->with('success', 'Spesialisasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specialization $specialization)
    {
        // Mencegah penghapusan jika spesialisasi masih digunakan
        if ($specialization->tailors()->count() > 0) {
            return back()->with('error', 'Gagal! Spesialisasi ini masih digunakan oleh satu atau lebih penjahit.');
        }
        
        $specialization->delete();

        return back()->with('success', 'Spesialisasi berhasil dihapus.');
    }
}