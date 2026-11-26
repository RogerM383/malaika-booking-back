<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateL5Swagger
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = config('l5-swagger.defaults.basic_auth.user');
        $password = config('l5-swagger.defaults.basic_auth.password');

        if (! is_string($user) || $user === '' || ! is_string($password) || $password === '') {
            return $this->unauthorized();
        }

        $userMatches = hash_equals($user, (string) $request->getUser());
        $passwordMatches = hash_equals($password, (string) $request->getPassword());

        if (! $userMatches || ! $passwordMatches) {
            return $this->unauthorized();
        }

        return $next($request);
    }

    private function unauthorized(): Response
    {
        return response('Unauthorized', 401, ['WWW-Authenticate' => 'Basic']);
    }
}
