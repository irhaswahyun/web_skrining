<?php

namespace App\Http\Controllers;

use App\Models\daftar_pertanyaan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class Daftar_PertanyaanController extends Controller
{
    public function index(Request $request)
    {
        $query = daftar_pertanyaan::with('jawaban');

        if ($request->search) {
            $query->where('pertanyaan', 'LIKE', '%' . $request->search . '%');
        }

        $pertanyaans = $query->get();

        return view('admin.daftar_pertanyaan.index', compact('pertanyaans'));
    }

    public function store(Request $request)
    {
        try {
            // $request->validate([
            //     'pertanyaan' => 'required',
            // ]);

            $pertanyaan = new daftar_pertanyaan();
            $pertanyaan->pertanyaan = $request->pertanyaan;
            $pertanyaan->save();

            return redirect()->route('daftar_pertanyaan.index')->with('success', 'Pertanyaan berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('daftar_pertanyaan.index')->with('error', $th->getMessage());
        }
    }

    public function edit($id)
    {
        $editData = daftar_pertanyaan::findOrFail($id);
        return view('admin.daftar_pertanyaan.edit', compact('editData'));
    }

    public function update(Request $request, $id)
    {
        $pertanyaan = daftar_pertanyaan::findOrFail($id);
        $pertanyaan->pertanyaan = $request->pertanyaan;
        $pertanyaan->save();

        return redirect()->route('daftar_pertanyaan.index')->with('success', 'Pertanyaan berhasil diperbarui');
    }

    public function delete(daftar_pertanyaan $id): RedirectResponse
    {
        $id->delete();
        return redirect()->route('daftar_pertanyaan.index')->with('success', 'Pertanyaan berhasil dihapus');
    }
}
