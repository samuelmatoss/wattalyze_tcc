<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class ReportExport implements FromArray
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return [
            ['Tipo', $this->data['tipo'] ?? ''],
            ['Formato', $this->data['formato'] ?? ''],
            ['Resumo', $this->data['resumo'] ?? ''],
            ['Dispositivos', implode(', ', $this->data['dispositivos'] ?? [])],
            ['Ambientes', implode(', ', $this->data['ambientes'] ?? [])],
        ];
    }
}
