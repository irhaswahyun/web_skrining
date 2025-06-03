<?php

namespace App\Http\Controllers;

use App\Models\FormSkrining; // Pastikan ini diimpor
use App\Models\Role;
use App\Models\Pasien; // Pastikan ini diimpor
use App\Models\Skrining; // Pastikan ini diimpor
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk query builder

class RoleController extends Controller
{
    public function admin() {
        // menampilkan jumlah data pasien
        $jumlahPasien = Pasien::count();

        // Menghitung total *jenis* skrining yang terdaftar dari model FormSkrining
        // Asumsi FormSkrining merepresentasikan definisi atau template skrining
        $jumlahFormSkrining = FormSkrining::count();

        // Menghitung total pasien unik yang sudah melakukan *setidaknya satu* skrining
        // Kita hitung NIK_Pasien yang unik dari tabel 'skrinings' (yang mencatat setiap riwayat skrining pasien)
        $jumlahPasienSkrining = Skrining::distinct('NIK_Pasien')->count();

        // --- Data untuk Kartu Skrining Dinamis (Jumlah Pasien per Jenis Skrining) ---
        // Kita perlu menggabungkan tabel 'skrinings' (riwayat skrining pasien)
        // dengan 'form_skrinings' (definisi jenis skrining, termasuk nama skriningnya).
        $skriningCounts = DB::table('skrinings') // Mulai dari tabel 'skrinings'
                            ->join('form_skrinings', 'skrinings.id_form_skrining', '=', 'form_skrinings.id')
                            ->select(
                                'form_skrinings.nama_skrining', // Ambil nama skrining dari form_skrinings
                                DB::raw('count(DISTINCT skrinings.NIK_Pasien) as patient_count') // Hitung NIK_Pasien yang unik
                            )
                            ->groupBy('form_skrinings.nama_skrining') // Kelompokkan berdasarkan nama jenis skrining
                            ->get(); // Ambil hasilnya sebagai koleksi

        return view('admin.adminDashboard', [ // Ubah 'admin.dashboard' menjadi 'admin.adminDashboard'
            'title' => 'Dashboard Admin',
            'jumlahPasien' => $jumlahPasien,
            'jumlahFormSkrining' => $jumlahFormSkrining,
            'jumlahPasienSkrining' => $jumlahPasienSkrining,
            'skriningCounts' => $skriningCounts // Tambahkan data kartu dinamis di sini
        ]);
    }

    public function nakes() {
        return view('nakes.nakesDashboard', [
            'title' => 'Dashboard Nakes'
        ]);
    }

    // ... (metode index, store, edit, update, delete untuk Role lainnya tetap sama) ...

    public function index(Request $request)
    {
        $query = Role::query();

        if ($request->search) {
            $query->where('Nama_Role', 'LIKE', '%' . $request->search . '%');
        }

        $roles = $query->get();

        return view('admin.role.index', compact('roles'));
    }

    public function store(Request $request)
    {
        try {
            $role = new Role();
            $role->Nama_Role = $request->Nama_Role;
            $role->save();

            return redirect()->route('role.index')->with('success', 'Role berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('role.index')->with('error', $th->getMessage());
        }
    }

    public function edit($id)
    {
        $editData = Role::findOrFail($id);
        return view('admin.role.edit', compact('editData'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->Nama_Role = $request->Nama_Role;
        $role->save();

        return redirect()->route('role.index')->with('success', 'Role berhasil diperbarui');
    }

    public function delete(Role $id): RedirectResponse
    {
        $id->delete();
        return redirect()->route('role.index')->with('success', 'Role berhasil dihapus');
    }
}