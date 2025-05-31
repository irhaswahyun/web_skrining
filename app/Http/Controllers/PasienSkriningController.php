<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Skrining;
use App\Models\FormSkrining;
use App\Models\Pasien;
use Illuminate\Support\Facades\Log; // Pastikan Log facade di-import

class PasienSkriningController extends Controller
{
    public function index(Request $request)
    {
        $Wilayah = $request->query('Wilayah');
        $namaFormSkrining = $request->query('nama_form_skrining');
        // Filter bulan dan tahun seharusnya diambil dari input 'bulan' dan 'tahun'
        // Namun, jika formatnya 'YYYY-MM', Anda perlu memisahkannya.
        // Berdasarkan image_dcee9c.jpg, filter bulan dan tahun adalah terpisah.
        // Jika dari frontend, Anda kirim 'bulan' (e.g., '05') dan 'tahun' (e.g., '2025')
        $bulan_param = $request->query('bulan'); // e.g., '05'
        $tahun_param = $request->query('tahun'); // e.g., '2025'


        // Dapatkan id form skrining dari nama form skrining
        $formSkrining = FormSkrining::where('nama_skrining', $namaFormSkrining)->first();

        if (!$formSkrining) {
            return redirect()->route('rekap_hasil_skrining.index')->with('error', 'Formulir skrining tidak ditemukan.');
        }

        $query = Skrining::with(['pasien', 'formSkrining.penyakit'])
                        ->whereHas('formSkrining', function ($q) use ($formSkrining) {
                            $q->where('id', $formSkrining->id);
                        });

        // Filter Wilayah
        if (!empty($Wilayah)) {
            if ($Wilayah === 'Tidak Diketahui') {
                $query->whereHas('pasien', function ($q) {
                    $q->whereNull('Wilayah')->orWhere('Wilayah', '');
                });
            } else {
                $query->whereHas('pasien', function ($q) use ($Wilayah) {
                    // Pastikan 'Wilayah' kapital sesuai DB.
                    // Jika ingin case-insensitive (disarankan), gunakan:
                    // $q->where(DB::raw('LOWER(Wilayah)'), '=', strtolower($Wilayah));
                    $q->where('Wilayah', $Wilayah);
                });
            }
        }

        // Filter Tanggal Skrining
        // Ganti logic filter tanggal ini agar sesuai dengan parameter bulan dan tahun terpisah
        if ($bulan_param && $tahun_param) {
            $query->whereYear('Tanggal_Skrining', $tahun_param)
                  ->whereMonth('Tanggal_Skrining', $bulan_param);
        } else {
            // Default filter ke bulan dan tahun saat ini jika tidak ada filter yang diberikan
            $query->whereYear('Tanggal_Skrining', now()->year)
                  ->whereMonth('Tanggal_Skrining', now()->month);
        }

        try {
            $pasienSkrining = $query->get()->map(function ($skrining) {
                $nik = $skrining->pasien->NIK ?? 'N/A';
                $namaPasien = $skrining->pasien->Nama_Pasien ?? 'N/A';
                $namaSkrining = $skrining->formSkrining->nama_skrining ?? 'N/A';

                $namaPenyakitTerkait = 'Tidak Diketahui';
                if ($skrining->formSkrining && $skrining->formSkrining->penyakit) {
                    $namaPenyakitTerkait = $skrining->formSkrining->penyakit->Nama_Penyakit;
                }

                return [
                    'id_skrining' => $skrining->id,
                    'NIK' => $nik,
                    'Nama_Pasien' => $namaPasien,
                    // SOLUSI UTAMA ADA DI BARIS BAWAH INI: Tambahkan null check!
                    'Tanggal_Skrining' => $skrining->Tanggal_Skrining ? $skrining->Tanggal_Skrining->format('d-m-Y') : 'N/A',
                    'Nama_Skrining' => $namaSkrining,
                    'Nama_Penyakit' => $namaPenyakitTerkait,
                    'Nama_Petugas' => $skrining->Nama_Petugas,
                ];
            });

            return view('admin.rekap_hasil_skrining.pasien_list', compact(
                'pasienSkrining', 'Wilayah', 'namaFormSkrining', 'bulan_param', 'tahun_param' // Ganti bulan dan tahun dengan parameter yang benar
            ));

        } catch (\Exception $e) {
            Log::error('Error loading pasien data for form (PasienSkriningController): ' . $namaFormSkrining . ' and Wilayah: ' . $Wilayah, [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('rekap_hasil_skrining.index')->with('error', 'Terjadi kesalahan saat memuat daftar pasien. Silakan hubungi administrator.');
        }
    }
}