<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\{FromView, ShouldAutoSize, Exportable};

class LaporanLogistikExport implements FromView, ShouldAutoSize
{
    use Exportable;

    protected $transaksis;
    protected $rekapBarang;
    protected $label;

    public function __construct($transaksis, $rekapBarang, $label)
    {
        $this->transaksis = $transaksis;
        $this->rekapBarang = $rekapBarang;
        $this->label = $label;
    }

    public function view(): View
    {
        return view('logistik.laporan_excel', [
            'transaksis' => $this->transaksis,
            'rekapBarang' => $this->rekapBarang,
            'label' => $this->label,
        ]);
    }
}
