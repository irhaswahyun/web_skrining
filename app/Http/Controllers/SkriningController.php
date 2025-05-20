<?php
namespace App\Http\Controllers;

use App\Models\DaftarPenyakit;
use App\Models\DaftarPertanyaan;
use App\Models\Skrining;
use App\Models\Penyakit;
use App\Models\Pertanyaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SkriningController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $skrinings = Skrining::with('pasien', 'penyakit')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('pasien', function ($subQuery) use ($search) {
                    $subQuery->where('Nama_Pasien', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('Tanggal_Skrining', 'desc')
            ->paginate(10);

        $penyakits = DaftarPenyakit::all();
        $pertanyaans = DaftarPertanyaan::all();

        if ($request->ajax()) {
            return view('admin.skrining.index', compact('skrinings', 'penyakits', 'pertanyaans'));
        }

        return view('admin.skrining.index', compact('skrinings', 'penyakits', 'pertanyaans'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Nama_Petugas' => 'required',
            'NIK_Pasien' => 'required',
            'Nama_Pasien' => 'required',
            'Tanggal_Skrining' => 'required|date',
            'id_penyakit' => 'nullable|exists:penyakits,id',
            'pertanyaan_ids' => 'array',
            'pertanyaan_ids.*' => 'exists:pertanyaans,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $skrining = Skrining::create([
                'Nama_Petugas' => $request->input('Nama_Petugas'),
                'NIK_Pasien' => $request->input('NIK_Pasien'),
                'Nama_Pasien' => $request->input('Nama_Pasien'),
                'Tanggal_Skrining' => $request->input('Tanggal_Skrining'),
                'id_penyakit' => $request->input('id_penyakit'),
            ]);

            if ($request->input('pertanyaan_ids')) {
                $skrining->pertanyaan()->attach($request->input('pertanyaan_ids'));
            }

            DB::commit();
            return response()->json(['message' => 'Data skrining berhasil disimpan.'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gagal menyimpan data skrining: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat menyimpan data skrining.'], 500);
        }
    }


     public function show($id)
    {
        try {
            $skrining = Skrining::with('pasien', 'penyakit', 'pertanyaan')->findOrFail($id);
            return response()->json($skrining);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
    }
        public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'Nama_Petugas' => 'required',
            'NIK_Pasien' => 'required',
            'Nama_Pasien' => 'required',
            'Tanggal_Skrining' => 'required|date',
            'id_penyakit' => 'nullable|exists:penyakits,id',
            'pertanyaan_ids' => 'array',
            'pertanyaan_ids.*' => 'exists:pertanyaans,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $skrining = Skrining::findOrFail($id);
            $skrining->update([
                'Nama_Petugas' => $request->input('Nama_Petugas'),
                'NIK_Pasien' => $request->input('NIK_Pasien'),
                'Nama_Pasien' => $request->input('Nama_Pasien'),
                'Tanggal_Skrining' => $request->input('Tanggal_Skrining'),
                'id_penyakit' => $request->input('id_penyakit'),
            ]);

            if ($request->input('pertanyaan_ids')) {
                $skrining->pertanyaan()->sync($request->input('pertanyaan_ids'));
            } else {
                $skrining->pertanyaan()->detach();
            }

            DB::commit();
            return response()->json(['message' => 'Data skrining berhasil diupdate.'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gagal update data skrining: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat mengupdate data skrining.'], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $skrining = Skrining::findOrFail($id);
            $skrining->pertanyaan()->detach();
            $skrining->delete();
            DB::commit();
            return response()->json(['message' => 'Data skrining berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gagal menghapus data skrining: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus data skrining.'], 500);
        }
    }

    public function getPertanyaanByPenyakit($id)
    {
        try {
            $penyakit = DaftarPenyakit::findOrFail($id);
            $pertanyaan = $penyakit->pertanyaan;
            return response()->json($pertanyaan);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Penyakit tidak ditemukan.'], 404);
        }
    }
}
