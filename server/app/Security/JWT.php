<?php

namespace App\Security;

use Firebase\JWT\JWT as FirebaseJWT;

class JWT
{
    private static string $algorithm = "";
    
    public static function create():string
    {
        $payload = [];
        $key = $_ENV['APP_KEY'] ?? '';

        return FirebaseJWT::encode($payload, $key, self::$algorithm);
    }

    public static function verify(string $token):bool
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
