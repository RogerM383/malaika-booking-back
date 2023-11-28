<?php

namespace App\Claims;

use App\Http\Resources\UserRole\UserRole;
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
        $token->addClaim('roles', $user->roles->pluck('id'));
        return $next($token);
    }
}
