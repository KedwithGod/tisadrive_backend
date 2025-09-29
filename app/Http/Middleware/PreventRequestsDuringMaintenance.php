<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventRequestsDuringMaintenance
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array<int, string>
     */
    protected $except = [
        'status',  // Example: Keep status route accessible
        'healthcheck',  // Example: Keep health check route accessible
        'api/*',  // Example: Allow all API routes during maintenance
        'login',  // Example: Allow login route
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the app is in maintenance mode using app() helper
        if (app()->isDownForMaintenance() && !in_array($request->path(), $this->except)) {
            // Redirect to a custom maintenance view if the route is not in the exception list
            return response()->view('maintenance', [], 503);
        }

        return $next($request);
    }
}
