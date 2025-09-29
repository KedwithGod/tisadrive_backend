<?php

namespace App\Exports;

use App\Models\Request;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;

class DutyExport implements FromView, ShouldAutoSize
{
    use Exportable;
    /**
     *
     */
    public function __construct($results) {
        $this->results = $results;
    }

    public function view(): View
    {
        return view('requests.driver_duty', [
            'results' => $this->results
        ]);
    }
}
