<?php

namespace App\Http\Controllers;

use App\Models\Daftar_Penyakit;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class Daftar_PenyakitController extends Controller
{
    public function index(Request $request)
    {
        $query = Daftar_Penyakit::query();

        if ($request->search) {
            $query->where('Nama_Penyakit', 'LIKE', '%' . $request->search . '%');
        }

        $daftarPenyakits = $query->get();

        return view('admin.daftar_penyakit.index', compact('daftarPenyakits'));
    }

    // public function create()
    // {
    //     return view('admin.daftar_penyakit.create');
    // }

    public function store(Request $request)
    {
        try {
            // $request->validate([
            //     'Nama_Penyakit' => 'required',
            // ]);

            $daftarPenyakit = new Daftar_Penyakit();
            $daftarPenyakit->Nama_Penyakit = $request->Nama_Penyakit;
            $daftarPenyakit->save();

            return redirect()->route('daftar_penyakit.index')->with('success', 'Daftar Penyakit berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('daftar_penyakit.index')->with('error', $th->getMessage());
        }
    }

    public function edit($id)
    {
        $editData = Daftar_Penyakit::findOrFail($id);
        return view('admin.daftar_penyakit.edit', compact('editData'));
    }

    public function update(Request $request, $id)
    {
        $daftarPenyakit = Daftar_Penyakit::findOrFail($id);
        $daftarPenyakit->Nama_Penyakit = $request->Nama_Penyakit;
        $daftarPenyakit->save();

        return redirect()->route('daftar_penyakit.index')->with('success', 'Daftar Penyakit berhasil diperbarui');
    }

    public function delete(Daftar_Penyakit $id): RedirectResponse
    {
        $id->delete();
        return redirect()->route('daftar_penyakit.index')->with('success', 'Daftar Penyakit berhasil dihapus');
    }
}