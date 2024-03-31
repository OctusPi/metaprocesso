<?php

namespace App\Security;
use App\Utils\Notify;

class Guardian
{
    public static function validateAccess(int $module_id = 0)
    {
        if(!self::checkToken()){
            return Response()->json(Notify::warning("Token de acesso inválido ou expirado"), 401);
        }

        if(!self::checkAccess($module_id)){
            return Response()->json(Notify::warning("Você não possui acesso a este módulo!"), 401);
        }
    }

    public static function getToken(): string
    {
        $authorization = Request()->server('HTTP_AUTHORIZATION');
        return str_replace('Bearer ', '', $authorization ?? '');
    }


    public static function checkToken(): bool
    {
        return JWT::validate(self::getToken());
    }

    public static function checkAccess(int $module_id = 0): bool
    {
        $user = JWT::decoded(self::getToken());
        
        if ($user == null) {
            return false;
        }

        return key_exists(strval($module_id), $user->modules);
    }
}
