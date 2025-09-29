<?php

namespace App\Exports;

use App\Models\Request;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;

class RequestExport implements FromView, ShouldAutoSize
{
    use Exportable;
    /**
     *
     */
    public function __construct($requests) {
        $this->requests = $requests;
    }

    public function view(): View
    {
        return view('requests.requests', [
            'results' => $this->requests
        ]);
    }
}
