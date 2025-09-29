<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Config;

class OwnersExport implements FromView, ShouldAutoSize
{
    use Exportable;
    /**
     *
     */
    public function __construct($owners) {
        $this->owners = $owners;
    }

    public function view(): View
    {
        return view('owners.owners', [
            'results' => $this->owners,
        ]);
    }
}
