<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BypassAuthInDevelopment
{
    /**
     * Handle an incoming request.
     *
     * In local/development environment, automatically authenticate as the first user.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('local') && ! Auth::check()) {
            $user = User::first();

            if ($user) {
                Auth::login($user);
            }
        }

        return $next($request);
    }
}
