<?php
namespace App\Security;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class Guardian
{
    public static function checkToken(?string $token): bool
    {
        if (!$token) {
            return false;
        }

        $tokenData = PersonalAccessToken::findToken($token);

        if (!$tokenData) {
            return false;
        }

        if ($tokenData->expires_at && now()->greaterThan($tokenData->expires_at)) {
            return false;
        }

        return true;
    }

    public static function getPersonalToken(string $token): ?PersonalAccessToken
    {
        if (!$token) {
            return null;
        }

        return PersonalAccessToken::findToken($token);
    }

    public static function getUserByToken(string $token): ?User
    {
        $dataToken = self::getPersonalToken($token);

        if (!$dataToken) {
            return null;
        }

        return User::find($dataToken->tokenable_id);
    }
}
