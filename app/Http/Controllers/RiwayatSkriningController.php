<?php

namespace App\Http\Controllers;

use App\Models\Skrining;
use App\Models\Pasien;
use App\Models\FormSkrining;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class RiwayatSkriningController extends Controller
{
    /**
     * Menampilkan halaman utama Riwayat Skrining.
     */
    public function index()
    {
        return view('admin.riwayat_skrining.index');
    }

    /**
     * Mengambil riwayat skrining berdasarkan NIK Pasien.
     * Dipanggil melalui AJAX.
     */
    public function getHistory(Request $request)
    {
        $nik = $request->input('nik');

        if (empty($nik)) {
            return response()->json(['success' => false, 'message' => 'NIK Pasien tidak boleh kosong.'], 400);
        }

        try {
            $pasien = Pasien::where('NIK', $nik)->first();

            if (!$pasien) {
                return response()->json(['success' => false, 'message' => 'Pasien dengan NIK tersebut tidak ditemukan.'], 404);
            }

            $riwayatSkrining = Skrining::with([
                'pasien',
                'formSkrining.penyakit',
                'jawabans.daftarPertanyaan'
            ])
            ->where('NIK_Pasien', $nik)
            ->orderBy('Tanggal_Skrining', 'asc')
            ->get();

            $totalSkriningDilakukan = $riwayatSkrining->count();
            $jenisSkriningDilakukanIds = $riwayatSkrining->pluck('id_form_skrining')->unique();
            $jumlahJenisSkriningDilakukan = $jenisSkriningDilakukanIds->count();

            // Mengembalikan total jenis skrining tersedia secara global, bukan berdasarkan kategori
            $totalJenisSkriningTersedia = FormSkrining::count();

            $formattedHistory = $riwayatSkrining->map(function ($skrining) {
                $pertanyaanJawaban = [];
                if ($skrining->formSkrining && $skrining->formSkrining->penyakit && $skrining->formSkrining->penyakit->pertanyaan) {
                    foreach ($skrining->formSkrining->penyakit->pertanyaan as $pertanyaan) {
                        $jawabanObj = $skrining->jawabans->firstWhere('ID_DaftarPertanyaan', $pertanyaan->id);
                        $pertanyaanJawaban[] = [
                            'pertanyaan' => $pertanyaan->pertanyaan,
                            'jawaban' => $jawabanObj ? $jawabanObj->jawaban : '-',
                        ];
                    }
                }

                return [
                    'id' => $skrining->id,
                    'NIK' => $skrining->NIK_Pasien,
                    'Nama_Pasien' => $skrining->Nama_Pasien,
                    'Nama_Petugas' => $skrining->Nama_Petugas,
                    'Nama_Skrining' => $skrining->formSkrining->nama_skrining ?? '-',
                    'Nama_Penyakit' => $skrining->formSkrining->penyakit->nama_penyakit ?? '-',
                    'Tanggal_Skrining' => $skrining->Tanggal_Skrining->format('Y-m-d'),
                    'Kondisi' => 'Sudah Skrining',
                    'detail_jawaban' => $pertanyaanJawaban,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedHistory,
                'summary' => [
                    // 'kategori_pasien' => $pasien->kategori ?? '-', // Kategori pasien dihapus
                    'total_skrining_dilakukan' => $totalSkriningDilakukan,
                    'jumlah_jenis_skrining_dilakukan' => $jumlahJenisSkriningDilakukan,
                    'total_jenis_skrining_tersedia' => $totalJenisSkriningTersedia, // Menggunakan total global
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching riwayat skrining: ' . $e->getMessage() . ' - ' . $e->getFile() . ' on line ' . $e->getLine());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengambil riwayat skrining. Silakan coba lagi.'], 500);
        }
    }
}
