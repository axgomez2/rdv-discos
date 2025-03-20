<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if(!Auth::check()){
            return redirect()->route('login');
        }

        $authUserRole = Auth::user()->role;

        switch($role){
            case 'user':
                if($authUserRole == 20){
                    return $next($request);
            }
            break;
            case 'resale':
                if($authUserRole == 40){
                    return $next($request);
            }
            break;
            case 'admin':
                if($authUserRole == 66){
                    return $next($request);
            }
            break;
        }

        switch($authUserRole){
            case 20:
                return redirect()->route('site.home');
            case 40:
                return redirect()->route('pdv.dashboard');
            case 66:
                return redirect()->route('admin.dashboard');
        }

        return redirect()->route('login');
    }
}
