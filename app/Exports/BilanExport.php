<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BilanExport implements WithMultipleSheets
{
    protected $statsGlobales;
    protected $statsParAgence;

    public function __construct($statsGlobales, $statsParAgence)
    {
        $this->statsGlobales = $statsGlobales;
        $this->statsParAgence = $statsParAgence;
    }

    public function sheets(): array
    {
        return [
            new BilanGlobalSheet($this->statsGlobales),
            new BilanAgenceSheet($this->statsParAgence),
        ];
    }
}
