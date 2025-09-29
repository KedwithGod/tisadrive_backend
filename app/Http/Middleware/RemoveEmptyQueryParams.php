<?php

namespace App\Http\Middleware;

use Closure;

class RemoveEmptyQueryParams
{
    public function handle($request, Closure $next)
    {
         // Get the current URL and query parameters
        $url = $request->url();
        $queryParams = $request->query();

        // Filter out null or empty values
        $filteredParams = array_filter($queryParams, function($value) {
            return !is_null($value) && $value !== '';
        });

        // If there are empty or null parameters removed, regenerate the URL
        if (count($filteredParams) !== count($queryParams)) {
            // Build a new URL with the filtered parameters
            $newUrl = $url . '?' . http_build_query($filteredParams);

            // Redirect to the new URL
            return redirect($newUrl);
        }

        // Continue to the next request if no changes
        return $next($request);
    }
}