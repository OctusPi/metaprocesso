<?php

namespace App\Security;

use App\Models\User;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT as FirebaseJWT;

class JWT
{
    private static string $algorithm = 'HS256';
    
    public static function create(User $data):string
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

    public static function decoded(?string $token):?User
    {
        try {
            if(!is_null($token)) {
                $key = env('APP_KEY', '');
                $stdUser = FirebaseJWT::decode($token, new Key($key, self::$algorithm));
                $user = User::make((array) $stdUser);
                return $user;
            }

            return null;
            
        } catch (\Throwable $th) {
            Log::error("Falha ao decodificar Token: ".$th->getMessage());
            return null;
        } 
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
