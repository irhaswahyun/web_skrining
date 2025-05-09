<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pasien;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PasienController extends Controller
{
    public function index(Request $request)
    {
        $query = Pasien::query();

        if ($request->search) {
            $query->where('Nama_Pasien', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('NIK', 'LIKE', '%' . $request->search . '%');
        }

        $pasiens = $query->get();

        return view('admin.manajemen_pasien.index', compact('pasiens'));
    }

    // public function create()
    // {
    //     return view('admin.manajemen_pasien.create');
    // }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'NIK' => 'required|unique:pasiens,NIK',
            'Nama_Pasien' => 'required',
            'Tanggal_Lahir' => 'required|date',
            'Kategori' => 'required',
            'Jenis_Kelamin' => 'required',
            'Alamat' => 'required',
            'No_telp' => 'required',
        ]);

        try {
            $pasien = new Pasien();
            $pasien->NIK = $request->NIK;
            $pasien->Nama_Pasien = $request->Nama_Pasien;
            $pasien->Tanggal_Lahir = $request->Tanggal_Lahir;
            $pasien->Kategori = $request->Kategori;
            $pasien->Jenis_Kelamin = $request->Jenis_Kelamin;
            $pasien->Alamat = $request->Alamat;
            $pasien->No_telp = $request->No_telp;
            $pasien->save();

            return redirect()->route('pasien.index')->with('success', 'Pasien berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('pasien.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage()); // Lebih baik tangkap Exception
        }
    }


    public function edit($id)
    {
        $editData=Pasien::findOrFail($id);
        return view('admin.manajemen_pasien.edit', compact('editData'));
    }

    public function update(Request $request, $id)
    {
        $pasien=Pasien::findOrFail($id);
        $pasien->NIK = $request->NIK;
        $pasien->Nama_Pasien = $request->Nama_Pasien;
        $pasien->Tanggal_Lahir = $request->Tanggal_Lahir;
        $pasien->Kategori = $request->Kategori;
        $pasien->Jenis_Kelamin = $request->Jenis_Kelamin;
        $pasien->Alamat = $request->Alamat;
        $pasien->No_telp = $request->No_telp;
        $pasien->save();

        return redirect()->route('pasien.index')->with('success', 'Role berhasil diperbarui');
    }

    public function delete(Pasien $id): RedirectResponse
    {

        $id->delete();
        return redirect()->route('pasien.index')->with('success', 'Role berhasil dihapus');
        
    }
}
