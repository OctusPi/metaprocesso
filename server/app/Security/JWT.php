<?php

namespace App\Security;

use App\Models\User;
use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

class JWT
{
    private static string $algorithm = 'HS256';
    
    public static function create($data):string
    {
        $key = env('APP_KEY', '');
        $url = env('APP_URL', '');
        
        $payload = [
            'iss' => $url,
            'aud' => $url,
            'exp' => time() + 3600,
            'iat' => time(),
            'data' => $data
        ];

        return FirebaseJWT::encode($payload, $key, self::$algorithm);
    }

    public static function validate(string $token):bool
    {
        try {
            $key = env('APP_KEY', '');
            FirebaseJWT::decode($token, new Key($key, self::$algorithm));
            return true;
        } catch (\Throwable $th) {
            return false;
        } 
    }
}
