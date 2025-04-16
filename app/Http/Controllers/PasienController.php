<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;   
use App\Models\Pasien;

class PasienController extends Controller
{
    public function index()
    {
        $pasiens = Pasien::all();
        return view('admin.manajemen_pasien.index', compact('pasiens'));
    }

    public function create()
    {
        return view('admin.manajemen_pasien.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|unique:pasiens,nik',
            'nama' => 'required|string|max:255',
        ]);

        Pasien::create([
            'nik' => $request->nik,
            'nama' => $request->nama,
        ]);

        return redirect()->route('manajemen_pasien.index')->with('success', 'Pasien berhasil ditambahkan.');
    }
}
