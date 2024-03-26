<?php

namespace App\Security;
use App\Utils\Notify;
use Illuminate\Support\Facades\Response;

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


    public static function checkToken(): bool
    {
        $authorization = Request()->server('HTTP_AUTHORIZATION');
        return JWT::validate($authorization);
    }

    public static function checkAccess(int $module_id = 0): bool
    {
        $authorization = Request()->server('HTTP_AUTHORIZATION');
        $user = JWT::decoded($authorization);
        
        if ($user == null) {
            return false;
        }

        return key_exists(strval($module_id), $user->modules);
    }
}
