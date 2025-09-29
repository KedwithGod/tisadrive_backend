<?php

namespace App\Exports;

use App\Models\User;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;

class UsersExport implements FromView, ShouldAutoSize
{
    use Exportable;
    /**
     *
     */
    public function __construct($users) {
        $this->users = $users;
    }

    public function view(): View
    {
        return view('users.users', [
            'results' => $this->users
        ]);
    }
}
