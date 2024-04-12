<?php

namespace App\Security;
use App\Models\User;
use App\Utils\Notify;
use Illuminate\Support\Facades\Log;
use Response;

class Guardian
{
    public static function validateAccess(int $module_id = 0): void
    {
        if(!self::checkAccessModuleID($module_id)){
            die();
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

    public static function checkAccess(string $module_name): bool
    {
        $user = self::getUser();
        if (!is_null($user)) {
            $auth_modules = array_column($user->modules, 'module');
            return in_array(str_replace('api/', '', $module_name), $auth_modules);
        }

        return false;
    }

    public static function checkAccessModuleID(int $module_id): bool
    {
        $user = self::getUser();
        if (!is_null($user)) {
            $auth_modules = array_column($user->modules, 'id');
            return in_array($module_id, $auth_modules);
        }

        return false;
    }

    public static function getUser():?User
    {
        return JWT::decoded(self::getToken());
    }
}
