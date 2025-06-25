<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
// En App\Http\Middleware\ForceJsonResponse.php
public function handle($request, Closure $next)
{
    $request->headers->set('Accept', 'application/json');
    $response = $next($request);

    // Forzar JSON en todas las respuestas
    if (!$response instanceof JsonResponse) {
        $response = response()->json([
            'error' => 'Respuesta inesperada',
            'original' => $response->getContent()
        ], 500);
    }

    return $response;
}
}
