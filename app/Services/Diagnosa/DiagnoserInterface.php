<?php
namespace App\Services\Diagnosa;

use App\Models\Skrining; // Pastikan ini ada

interface DiagnoserInterface
{
    public function analyze(Skrining $skrining): array;
}