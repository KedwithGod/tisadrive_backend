<?php

namespace App\Exports;

use App\Models\Admin\Driver;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Config;

class DriversExport implements FromView, ShouldAutoSize
{
    use Exportable;
    /**
     *
     */
    public function __construct($drivers) {
        $this->drivers = $drivers;
    }

    public function view(): View
    {
        return view('drivers.drivers', [
            'results' => $this->drivers,
        ]);
    }
}
