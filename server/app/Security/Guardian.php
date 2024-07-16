<?php

namespace App\Security;

use Laravel\Sanctum\PersonalAccessToken;

class Guardian
{

    public static function checkToken(?string $token): bool
    {
        $tokenData = PersonalAccessToken::findToken($token);

        if (!$tokenData) {
            return false;
        }

        if ($tokenData->expires_at && now()->greaterThan($tokenData->expires_at)) {
            return false;
        }

        return true;

    }
}
