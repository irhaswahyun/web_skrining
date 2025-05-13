<?php

namespace App\Http\Controllers;

use App\Models\PenyakitPertanyaan;
use Illuminate\Http\Request;
use App\Models\Skrining;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SkriningController extends Controller
{
    public function index(Request $request)
    {
        $penyakit_pertanyaans = PenyakitPertanyaan::with('formSkrining')->get();
        $query = Skrining::query();

        if ($request->search) {
            $query->where('Nama_Petugas', 'LIKE', '%' . $request->search . '%')
                ->orWhere('NIK_Pasien', 'LIKE', '%' . $request->search . '%')
                ->orWhere('Nama_Pasien', 'LIKE', '%' . $request->search . '%')
                ->orWhere('ID_penyakit_pertanyaan', 'LIKE', '%' . $request->search . '%');
        }

        $skrinings = $query->get();

        return view('admin.skrining.index', compact('skrinings', 'penyakit_pertanyaans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'Nama_Petugas' => 'required',
            'NIK_Pasien' => 'required',
            'Nama_Pasien' => 'required',
            'Tanggal_Skrining' => 'required|date',
            'ID_penyakit_pertanyaan' => 'required|exists:penyakit_pertanyaans,id',
        ]);

        try {
            $skrining = new Skrining();
            $skrining->Nama_Petugas = $request->Nama_Petugas;
            $skrining->NIK_Pasien = $request->NIK_Pasien;
            $skrining->Nama_Pasien = $request->Nama_Pasien;
            $skrining->Tanggal_Skrining = $request->Tanggal_Skrining;
            $skrining->ID_penyakit_pertanyaan = $request->ID_penyakit_pertanyaan;
            $skrining->save();

            return redirect()->route('skrining.index')->with('success', 'Data skrining berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('skrining.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nama_Petugas' => 'required',
            'NIK_Pasien' => 'required',
            'Nama_Pasien' => 'required',
            'Tanggal_Skrining' => 'required|date',
            'ID_penyakit_pertanyaan' => 'required',
        ]);

        try {
            $skrining = Skrining::findOrFail($id);
            $skrining->Nama_Petugas = $request->Nama_Petugas;
            $skrining->NIK_Pasien = $request->NIK_Pasien;
            $skrining->Nama_Pasien = $request->Nama_Pasien;
            $skrining->Tanggal_Skrining = $request->Tanggal_Skrining;
            $skrining->ID_penyakit_pertanyaan = $request->ID_DaftarPertanyaan;
            $skrining->save();

            return redirect()->route('skrining.index')->with('success', 'Data skrining berhasil diperbarui');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('skrining.index')->with('error', 'Data skrining tidak ditemukan');
        } catch (\Exception $e) {
            return redirect()->route('skrining.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete(Skrining $skrining): RedirectResponse
    {
        try {
            $skrining->delete();
            return redirect()->route('skrining.index')->with('success', 'Data skrining berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('skrining.index')->with('error', 'Terjadi kesalahan saat menghapus data skrining: ' . $e->getMessage());
        }
    }
}