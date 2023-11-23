<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SetJsonContentType
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        Log::debug(json_encode($request->header()));

        // Asegúrate de que la respuesta sea JSON y establece el encabezado Content-Type si no está presente.
        if (!$response->headers->has('Content-Type')) {
            $response->header('Content-Type', 'application/json');
        }

        return $response;
    }
}
