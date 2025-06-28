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

                // Memuat relasi diagnosa dan formSkrining untuk ditampilkan di tabel riwayat
                $riwayatSkrining = Skrining::where('NIK_Pasien', $nik)
                    ->with('formSkrining', 'pasien', 'diagnosa') // Tambahkan 'diagnosa' di sini
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

            $riwayatSkriningData = collect(); // Menggunakan nama berbeda agar tidak bentrok
            $pasienFound = Pasien::where('NIK', $nik)->first();

            // Inisialisasi variabel summary dengan nilai default
            $totalSkriningDilakukan = 0;
            $totalJenisSkriningTersedia = FormSkrining::count(); // Ini harus selalu ada

            if ($pasienFound) {
                $skrinings = Skrining::where('NIK_Pasien', $nik)
                    ->with([
                        'formSkrining',
                        'pasien',
                        'jawabans.pertanyaan', // Sudah diperbaiki dari 'daftarPertanyaan' ke 'pertanyaan'
                        'diagnosa' // <<<--- PENTING: Muat relasi diagnosa di sini!
                    ])
                    ->orderBy('Tanggal_Skrining', 'desc')
                    ->get();

                $riwayatSkriningData = $skrinings->map(function ($skrining) {
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

                    // <<<--- PENTING: Ambil 'Kondisi' dari relasi diagnosa->hasil_utama
                    $kondisi = $skrining->diagnosa ? ($skrining->diagnosa->hasil_utama ?? '-') : '-';

                    $detailJawaban = [];
                    // Logika ini akan mengambil pertanyaan dari formSkrining dan mencocokkan dengan jawabans yang dimiliki skrining.
                    // Pastikan formSkrining->pertanyaan dan jawabans.pertanyaan sudah dimuat.
                    if ($skrining->formSkrining && $skrining->formSkrining->pertanyaan) {
                        foreach ($skrining->formSkrining->pertanyaan as $pertanyaan) {
                            // Mencari jawaban yang cocok berdasarkan ID_DaftarPertanyaan
                            // Asumsi $jawaban->pertanyaan adalah relasi yang sudah dimuat.
                            $jawabanObj = $skrining->jawabans->firstWhere('ID_DaftarPertanyaan', $pertanyaan->id);
                            $detailJawaban[] = [
                                'pertanyaan' => $pertanyaan->pertanyaan, // Mengambil teks pertanyaan dari model DaftarPertanyaan
                                'jawaban' => $jawabanObj->jawaban ?? '-', // Mengambil jawaban dari model Jawaban
                            ];
                        }
                    }

                    return [
                        'id' => $skrining->id,
                        'Nama_Petugas' => $skrining->Nama_Petugas ?? '-',
                        'NIK_Pasien' => $skrining->NIK_Pasien ?? '-',
                        'Nama_Pasien' => $skrining->Nama_Pasien ?? '-',
                        'Tanggal_Skrining' => $tanggalFormatted,
                        'Nama_Skrining_Form' => $skrining->formSkrining->nama_skrining ?? '-', // Mengambil nama skrining dari relasi
                        'Kondisi' => $kondisi, // Mengirimkan kondisi dari diagnosa
                        'detail_jawaban' => $detailJawaban, // Data pertanyaan & jawaban untuk modal detail
                    ];
                });

                // Hitung total skrining dilakukan jika pasien ditemukan dan ada riwayat
                $totalSkriningDilakukan = $skrinings->count(); // Menggunakan $skrinings untuk hitungan
            } else {
                return response()->json(['success' => false, 'message' => 'NIK Pasien tidak ditemukan.'], 404);
            }

            return response()->json([
                'success' => true,
                'riwayat' => $riwayatSkriningData, // Menggunakan data yang sudah dimapping
                'pasien_data' => $pasienFound ? [
                    'NIK' => $pasienFound->NIK,
                    'Nama_Pasien' => $pasienFound->Nama_Pasien,
                ] : null,
                'summary' => [
                    'total_skrining_dilakukan' => $totalSkriningDilakukan,
                    'total_jenis_skrining_tersedia' => $totalJenisSkriningTersedia,
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'NIK Pasien diperlukan.'], 400);
        } catch (\Exception $e) {
            \Log::error('Error fetching riwayat skrining: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengambil riwayat skrining. Silakan coba lagi. Detil: ' . $e->getMessage()], 500);
        }
    }
}
