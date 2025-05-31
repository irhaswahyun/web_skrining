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

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'NIK' => 'required|unique:pasiens,NIK|digits:16', // Tambahkan validasi digits
            'Nama_Pasien' => 'required',
            'Tanggal_Lahir' => 'required|date',
            'Kategori' => 'required',
            'Jenis_Kelamin' => 'required',
            'Alamat' => 'required',
            'Wilayah' => 'required',
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
            $pasien->Wilayah = $request->Wilayah;
            $pasien->No_telp = $request->No_telp;
            $pasien->save();

            return redirect()->route('pasien.index')->with('success', 'Pasien berhasil ditambahkan');
        } catch (\Exception $e) {
            // Log error untuk debugging lebih lanjut
            // \Log::error('Error saat menyimpan pasien: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('pasien.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        // Karena primaryKey model adalah NIK, $id di sini akan dicari di kolom NIK
        $editData = Pasien::findOrFail($id);
        return view('admin.manajemen_pasien.edit', compact('editData'));
    }

    public function update(Request $request, $id)
    {
        // Karena primaryKey model adalah NIK, $id di sini akan dicari di kolom NIK
        $pasien = Pasien::findOrFail($id);

        $request->validate([
            // PENTING: Mengubah cara mengabaikan NIK saat update
            // Gunakan $pasien->getKey() atau $pasien->NIK karena NIK adalah primaryKey
            'NIK' => 'required|digits:16|unique:pasiens,NIK,' . $pasien->getKey() . ',' . $pasien->getKeyName(),
            'Nama_Pasien' => 'required',
            'Tanggal_Lahir' => 'required|date',
            'Kategori' => 'required',
            'Jenis_Kelamin' => 'required',
            'Alamat' => 'required',
            'Wilayah' => 'required',
            'No_telp' => 'required',
        ]);

        try {
            $pasien->NIK = $request->NIK;
            $pasien->Nama_Pasien = $request->Nama_Pasien;
            $pasien->Tanggal_Lahir = $request->Tanggal_Lahir;
            $pasien->Kategori = $request->Kategori;
            $pasien->Jenis_Kelamin = $request->Jenis_Kelamin;
            $pasien->Alamat = $request->Alamat;
            $pasien->Wilayah = $request->Wilayah;
            $pasien->No_telp = $request->No_telp;
            $pasien->save();

            return redirect()->route('pasien.index')->with('success', 'Pasien berhasil diperbarui');
        } catch (\Exception $e) {
            // Log error untuk debugging lebih lanjut
            // \Log::error('Error saat memperbarui pasien: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('pasien.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete(Pasien $id): RedirectResponse
    {
        // Karena primaryKey model adalah NIK, $id di sini akan di-bind ke model Pasien berdasarkan NIK
        try {
            $id->delete();
            return redirect()->route('pasien.index')->with('success', 'Pasien berhasil dihapus');
        } catch (\Exception $e) {
            // \Log::error('Error saat menghapus pasien: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('pasien.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function getPasienData(Request $request) {
        $nik = $request->input('NIK');

        try {
            $pasien = Pasien::where('NIK', $nik)->firstOrFail();
            return response()->json(['success' => true, 'data' => $pasien]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Pasien tidak ditemukan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
