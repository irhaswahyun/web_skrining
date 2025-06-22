<?php

namespace App\Http\Controllers;

use App\Models\FormSkrining;
use App\Models\Role;
use App\Models\Pasien;
use App\Models\Skrining;
use App\Models\User;
use App\Models\DaftarPenyakit; // <-- PASTIKAN INI ADA JIKA ANDA INGIN MENAMPILKAN JUMLAH PENYAKIT
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            Log::warning('Unauthorized access attempt to dashboard: No user authenticated.');
            return redirect('/login')->with('error', 'Silakan login untuk mengakses dashboard.');
        }

        $roleName = null;
        try {
            $roleName = $user->role; // Ini akan memanggil getRoleAttribute() dan mengembalikan string role
        } catch (\Throwable $th) {
            Log::error("Failed to get role name for User ID: {$user->id}. Error: " . $th->getMessage());
            return redirect('/login')->with('error', 'Role pengguna tidak ditemukan atau tidak valid. Harap hubungi administrator.');
        }

        // --- Variabel Dashboard Umum (akan diisi untuk semua role yang masuk dashboard) ---
        $title = 'Dashboard';
        $jumlahPasien = Pasien::count();
        $jumlahFormSkrining = FormSkrining::count();
        $jumlahPasienSkrining = Skrining::distinct('NIK_Pasien')->count();
        $jumlahPenyakitTerdaftar = DaftarPenyakit::count(); // <-- PASTIKAN MODEL INI DIIMPOR DI ATAS

        $skriningCounts = DB::table('skrinings')
                            ->join('form_skrinings', 'skrinings.id_form_skrining', '=', 'form_skrinings.id')
                            ->select(
                                'form_skrinings.nama_skrining',
                                DB::raw('count(DISTINCT skrinings.NIK_Pasien) as patient_count')
                            )
                            ->groupBy('form_skrinings.nama_skrining')
                            ->get();

        $wilayahList = Pasien::distinct('Wilayah')
                             ->pluck('Wilayah')
                             ->filter()
                             ->sort()
                             ->values()
                             ->all();

        $jenisSkriningList = FormSkrining::distinct('nama_skrining')
                                         ->pluck('nama_skrining')
                                         ->filter()
                                         ->sort()
                                         ->values()
                                         ->all();

        // Variabel ini tidak diisi/digunakan jika Anda tidak ingin fitur per-Nakes,
        // tapi diinisialisasi agar tidak ada error 'Undefined variable' di Blade
        $totalPasienNakes = 0;
        $totalSkriningNakes = 0;
        $latestSkriningsByNakes = collect();

        if ($roleName === User::ROLE_ADMIN) {
            $title = 'Dashboard Admin';
        } elseif ($roleName === User::ROLE_NAKES) {
            $title = 'Dashboard Tenaga Kesehatan';
        } else {
            Log::info("User ID: {$user->id} logged in with unknown role: {$roleName}");
            return redirect('/')->with('info', 'Role Anda tidak dikenali. Menampilkan dashboard default.');
        }

        return view('admin.adminDashboard', compact(
            'title',
            'jumlahPasien',
            'jumlahFormSkrining',
            'jumlahPasienSkrining',
            'jumlahPenyakitTerdaftar', // <-- PASTIKAN INI DI-COMPACT
            'skriningCounts',
            'wilayahList',
            'jenisSkriningList',
            'totalPasienNakes',
            'totalSkriningNakes',
            'latestSkriningsByNakes',
            'roleName'
        ));
    }

    public function getSkriningDataForChart(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 403);
        }

        $roleName = null;
        try {
            $roleName = $user->role; // CUKUP $user->role SAJA
        } catch (\Throwable $th) {
            Log::error("Failed to get role name for chart data for User ID: {$user->id}. Error: " . $th->getMessage());
            return response()->json(['error' => 'Role pengguna tidak valid.'], 403);
        }

        $request->validate([
            'wilayah' => 'required|string',
            'nama_skrining' => 'required|string',
            'year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
        ]);

        $wilayah = $request->input('wilayah');
        $namaSkrining = $request->input('nama_skrining');
        $year = $request->input('year', date('Y'));

        Log::info("Fetching chart data for Wilayah: {$wilayah}, Skrining: {$namaSkrining}, Year: {$year} by Role: {$roleName}");

        $query = DB::table('skrinings')
                    ->join('pasiens', 'skrinings.NIK_Pasien', '=', 'pasiens.NIK')
                    ->join('form_skrinings', 'skrinings.id_form_skrining', '=', 'form_skrinings.id')
                    ->select(
                        DB::raw('MONTH(skrinings.Tanggal_Skrining) as month'),
                        DB::raw('COUNT(DISTINCT skrinings.NIK_Pasien) as patient_count')
                    )
                    ->where('pasiens.Wilayah', $wilayah)
                    ->where('form_skrinings.nama_skrining', $namaSkrining)
                    ->whereYear('skrinings.Tanggal_Skrining', $year);

        // Baris ini dihapus karena Anda tidak ingin memfilter berdasarkan user_id di dashboard
        // if ($roleName === User::ROLE_NAKES) {
        //     $query->where('skrinings.user_id', $user->id);
        // }

        $data = $query->groupBy(DB::raw('MONTH(skrinings.Tanggal_Skrining)'))
                      ->orderBy('month')
                      ->get();

        Log::info("Query result for {$wilayah} - {$namaSkrining} (Role: {$roleName}): " . $data->toJson());

        $monthlyData = array_fill(0, 12, 0);
        foreach ($data as $item) {
            $monthlyData[$item->month - 1] = $item->patient_count;
        }

        Log::info("Final monthly data for {$wilayah} - {$namaSkrining} (Role: {$roleName}): " . json_encode($monthlyData));

        return response()->json([
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'data' => $monthlyData,
            'wilayah' => $wilayah,
            'skriningName' => $namaSkrining
        ]);
    }

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