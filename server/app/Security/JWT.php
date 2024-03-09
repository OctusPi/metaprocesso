<?php

namespace App\Security;

use App\Models\User;
use Firebase\JWT\JWT as FirebaseJWT;

class JWT
{
    private static string $algorithm = 'HS256';
    
    public static function create(User $data):string
    {
        $payload = [
            'exp' => time() + 3600,
            'iat' => time(),
            'data' => $data
        ];
        $key = $_ENV['APP_KEY'] ?? '';

        return FirebaseJWT::encode($payload, $key, self::$algorithm);
    }

    public static function validate(string $token):bool
    {
        try {
            $key = $_ENV['APP_KEY'] ?? '';
            FirebaseJWT::decode($token, []);
            return true;
        } catch (\Throwable $th) {
            return false;
        } 
    }
}
