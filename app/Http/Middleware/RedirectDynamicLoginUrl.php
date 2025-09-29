<?php

namespace App\Http\Middleware;

use Closure;

class RedirectDynamicLoginUrl
{
    public function handle($request, Closure $next)
    {
        
        // Get the current URL and query parameters
        $url = $request->path();
        
        if($url=='login'){
            return redirect('login/'.get_settings('user_login'));
        }
        // Continue to the next request if no changes
        return $next($request);
    }
}