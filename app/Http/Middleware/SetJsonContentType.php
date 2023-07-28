<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetJsonContentType
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Asegúrate de que la respuesta sea JSON y establece el encabezado Content-Type si no está presente.
        if (!$response->headers->has('Content-Type')) {
            $response->header('Content-Type', 'application/json');
        }

        return $response;
    }
}
