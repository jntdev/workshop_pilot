<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAssistantToken
{
    /**
     * Autorise la requête uniquement si le jeton Bearer correspond au jeton demandé
     * (« read » ou « relay » dans config/assistant.php), et journalise chaque appel accepté.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $scope): Response
    {
        $expected = (string) config("assistant.{$scope}_token");
        $provided = (string) $request->bearerToken();

        if ($expected === '' || $provided === '' || ! hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        Log::channel('single')->info('assistant-api', [
            'scope' => $scope,
            'method' => $request->method(),
            'path' => $request->path(),
            'ip' => $request->ip(),
        ]);

        return $next($request);
    }
}
