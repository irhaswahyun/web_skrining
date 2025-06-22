<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Skrining;
use App\Models\Pasien;
use App\Models\FormSkrining;
use App\Models\DaftarPertanyaan;
use Carbon\Carbon;

class RiwayatSkriningController extends Controller
{
    public function index(Request $request)
    {
        $nik = $request->input('nik');
        $riwayatSkrining = collect();
        $pasienData = null;

        if ($nik) {
            $pasien = Pasien::where('NIK', $nik)->first();

            if ($pasien) {
                $pasienData = $pasien;

                $riwayatSkrining = Skrining::where('NIK_Pasien', $nik)
                    ->with('formSkrining', 'pasien')
                    ->orderBy('Tanggal_Skrining', 'desc')
                    ->get();
            }
        }

        return view('admin.riwayat_skrining.index', compact('riwayatSkrining', 'pasienData', 'nik'));
    }

    public function getHistory(Request $request)
    {
        try {
            $request->validate([
                'nik_pasien' => 'required|string|max:255',
            ]);

            $nik = $request->input('nik_pasien');

            $riwayatSkrining = collect();
            $pasienFound = Pasien::where('NIK', $nik)->first();

            // Inisialisasi variabel summary dengan nilai default
            $totalSkriningDilakukan = 0;
            $totalJenisSkriningTersedia = FormSkrining::count(); // Ini harus selalu ada

            if ($pasienFound) {
                $skrinings = Skrining::where('NIK_Pasien', $nik)
                    ->with([
                        'formSkrining',
                        'pasien',
                        'jawabans.daftarPertanyaan'
                    ])
                    ->orderBy('Tanggal_Skrining', 'desc')
                    ->get();

                $riwayatSkrining = $skrinings->map(function ($skrining) {
                    $tanggalFormatted = null;
                    try {
                        if ($skrining->Tanggal_Skrining instanceof Carbon) {
                            $tanggalFormatted = $skrining->Tanggal_Skrining->format('d-m-Y');
                        } else if ($skrining->Tanggal_Skrining) {
                            $tanggalFormatted = Carbon::parse($skrining->Tanggal_Skrining)->format('d-m-Y');
                        }
                    } catch (\Exception $e) {
                        $tanggalFormatted = 'Tanggal Tidak Valid';
                    }

                    $kondisi = $skrining->Kondisi ?? '-'; // Asumsi kolom 'Kondisi' ada di model Skrining atau default
                    // Jika 'Kondisi' dihitung, Anda perlu memanggil fungsi perhitungan di sini.

                    $detailJawaban = [];
                    if ($skrining->formSkrining && $skrining->formSkrining->pertanyaan) {
                        foreach ($skrining->formSkrining->pertanyaan as $pertanyaan) {
                            $jawabanObj = $skrining->jawabans->firstWhere('ID_DaftarPertanyaan', $pertanyaan->id);
                            $detailJawaban[] = [
                                'pertanyaan' => $pertanyaan->pertanyaan,
                                'jawaban' => $jawabanObj->jawaban ?? '-',
                            ];
                        }
                    }

                    return [
                        'id' => $skrining->id,
                        'Nama_Petugas' => $skrining->Nama_Petugas ?? '-',
                        'NIK_Pasien' => $skrining->NIK_Pasien ?? '-',
                        'Nama_Pasien' => $skrining->Nama_Pasien ?? '-',
                        'Tanggal_Skrining' => $tanggalFormatted,
                        'Nama_Skrining_Form' => $skrining->formSkrining->nama_skrining ?? '-',
                        'Kondisi' => $kondisi,
                        'detail_jawaban' => $detailJawaban,
                    ];
                });

                // Hitung total skrining dilakukan jika pasien ditemukan dan ada riwayat
                $totalSkriningDilakukan = $riwayatSkrining->count();

            } else {
                return response()->json(['success' => false, 'message' => 'NIK Pasien tidak ditemukan.'], 404);
            }

            return response()->json([
                'success' => true,
                'riwayat' => $riwayatSkrining,
                'pasien_data' => $pasienFound ? [
                    'NIK' => $pasienFound->NIK,
                    'Nama_Pasien' => $pasienFound->Nama_Pasien,
                ] : null,
                'summary' => [
                    'total_skrining_dilakukan' => $totalSkriningDilakukan, // Gunakan variabel yang sudah diinisialisasi
                    'total_jenis_skrining_tersedia' => $totalJenisSkriningTersedia, // Gunakan variabel yang sudah diinisialisasi
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'NIK Pasien diperlukan.'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengambil riwayat skrining. Silakan coba lagi. Detil: ' . $e->getMessage()], 500);
        }
    }
}
