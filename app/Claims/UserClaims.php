<?php

namespace App\Claims;

use CorBosman\Passport\AccessToken;
use Illuminate\Support\Facades\Auth;

class UserClaims
{
    public function handle(AccessToken $token, $next)
    {
        $user = Auth::user();
        $token->addClaim('id', $user->id);
        $token->addClaim('name', $user->name);
        $token->addClaim('email', $user->email);
        return $next($token);
    }
}
