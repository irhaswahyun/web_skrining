<?php

namespace App\Http\Controllers;

use App\Models\Skrining;
use Illuminate\Http\Request;
use App\Models\Pasien;


class SkriningController extends Controller
{
    public function index(Request $request)
    {
        $query = Skrining::query();

        if ($request->search) {
            $query->where('nama_pasien', 'like', '%' . $request->search . '%');
        }

        $skrinings = $query->get();

        return view('admin.skrining.index', compact('skrinings'));
    }

    public function create()
    {
        $pasiens = Pasien::all();
        return view('admin.skrining.create', compact('pasiens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pasien_id' => 'required|exists:pasiens,id',
            'jenis_skrining' => 'required|string|max:255',
        ]);

        Skrining::create([
            'pasien_id' => $request->pasien_id,
            'jenis_skrining' => $request->jenis_skrining,
        ]);

        return redirect()->route('skrining.index')->with('success', 'Data skrining berhasil ditambahkan.');
    }
}
