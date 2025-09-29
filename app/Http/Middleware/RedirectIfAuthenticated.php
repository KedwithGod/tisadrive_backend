<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {

        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {

            if (Auth::guard($guard)->check()) {
                
            
                if(access()->hasRole('user')){

                $access_login = Setting::where('name','user_login')->pluck('value')->first();

                $access_login = "login/".$access_login;

                if(request()->path()!==$access_login){

                    auth('web')->logout();

                    return redirect()->guest(request()->path());
                }else {
                return redirect('/create-booking');

                }

                }elseif (access()->hasRole('dispatcher')) {


                    $access_login = Setting::where('name','dispatcher_login')->pluck('value')->first();

                $access_login = "login/".$access_login;

                if(request()->path()!==$access_login){

                    auth('web')->logout();

                    return redirect()->guest(request()->path());
                }else {
                return redirect('/dispatcher/bookride');

                }

                }elseif (access()->hasRole('owner')) {


                    $access_login = Setting::where('name','owner_login')->pluck('value')->first();

                $access_login = "login/".$access_login;

                if(request()->path()!==$access_login){

                    auth('web')->logout();

                    return redirect()->guest(request()->path());
                }else {
                return redirect('/owner-dashboard');

                }

                
                }else{
                
                $access_login = Setting::where('name','admin_login')->pluck('value')->first();

                $access_login = "login/".$access_login;

                if(request()->path()!==$access_login){

                    auth('web')->logout();

                    return redirect()->guest(request()->path());
                }else {
                return redirect('/dashboard');

                }

                
                }

            }
        }

        return $next($request);
    }
}
