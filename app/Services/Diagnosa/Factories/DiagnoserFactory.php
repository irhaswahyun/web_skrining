<?php
namespace App\Services\Diagnosa\Factories; // PASTIKAN INI SESUAI DENGAN LOKASI FILE

use App\Services\Diagnosa\DiagnoserInterface; // PASTIKAN INI DI-IMPORT
use App\Services\Diagnosa\MalariaDiagnoser;   // PASTIKAN INI DI-IMPORT

class DiagnoserFactory
{
    protected array $diagnosers = [
        'skrining malaria' => MalariaDiagnoser::class,
    ];

    public function getDiagnoser(string $jenisPenyakit): ?DiagnoserInterface 
    {
        $jenisPenyakit = strtolower($jenisPenyakit);

        if (isset($this->diagnosers[$jenisPenyakit])) {
            $diagnoserClass = $this->diagnosers[$jenisPenyakit];
            if (class_exists($diagnoserClass)) {
                return new $diagnoserClass();
            }
        }
        return null;
    }
}