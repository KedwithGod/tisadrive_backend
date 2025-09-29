<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use App\Models\Setting;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        
        $admin_url = Setting::where('name','admin_login')->pluck('value')->first();

        $admin_url = "login/".$admin_url;

        return $request->expectsJson() ? null : redirect()->guest($admin_url);
    }
}
