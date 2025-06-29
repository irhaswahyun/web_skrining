<?php

namespace App\Http\Controllers;

use App\Models\DaftarPertanyaan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class Daftar_PertanyaanController extends Controller
{
    public function index(Request $request)
    {
        $query = DaftarPertanyaan::with('jawabans');

        if ($request->search) {
            $query->where('pertanyaan', 'LIKE', '%' . $request->search . '%');
        }

        $pertanyaans = $query->get();

        return view('admin.daftar_pertanyaan.index', compact('pertanyaans'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'pertanyaan' => 'required|string',
                'catatan'    => 'nullable|string',
            ]);

            $pertanyaan = new DaftarPertanyaan();
            $pertanyaan->pertanyaan = $request->pertanyaan;
            if ($request->catatan) { // Tambahkan kondisi ini
                $pertanyaan->catatan = substr($request->catatan, 0, 255);
            } else {
                 $pertanyaan->catatan = null;
            }
            $pertanyaan->save();

            return redirect()->route('daftar_pertanyaan.index')->with('success', 'Pertanyaan berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('daftar_pertanyaan.index')->with('error', $th->getMessage());
        }
    }

    public function edit($id)
    {
        $editData = DaftarPertanyaan::findOrFail($id);
        return view('admin.daftar_pertanyaan.edit', compact('editData'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pertanyaan' => 'required|string',
            'catatan'    => 'nullable|string',
        ]);
        $pertanyaan = DaftarPertanyaan::findOrFail($id);
        $pertanyaan->pertanyaan = $request->pertanyaan;
        if ($request->catatan) {  // Tambahkan kondisi ini
             $pertanyaan->catatan = substr($request->catatan, 0, 255);
        }
        else{
            $pertanyaan->catatan = null;
        }
        $pertanyaan->save();

        return redirect()->route('daftar_pertanyaan.index')->with('success', 'Pertanyaan berhasil diperbarui');
    }

    public function delete(DaftarPertanyaan $id): RedirectResponse
    {
        $id->delete();
        return redirect()->route('daftar_pertanyaan.index')->with('success', 'Pertanyaan berhasil dihapus');
    }
}
