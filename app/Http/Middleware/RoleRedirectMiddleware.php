<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleRedirectMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $role = $user->role;

            switch ($role) {
                case 'pengurus':
                    if (!$request->routeIs('pengurus.*')) {
                        return redirect()->route('pengurus.dashboard');
                    }
                    break;

                case 'guru':
                    if (!$request->routeIs('guru.*')) {
                        return redirect()->route('guru.dashboard');
                    }
                    break;

                case 'orangtua':
                    if (!$request->routeIs('orangtua.*')) {
                        return redirect()->route('orangtua.dashboard');
                    }
                    break;

                default:
                    Auth::logout();
                    return redirect('/login');
            }
        }

        return $next($request);
    }
}
