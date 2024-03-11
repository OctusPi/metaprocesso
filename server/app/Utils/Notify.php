<?php

namespace App\Utils;

class Notify
{
    const SUCCESS = 'success';
    const WARNING = 'warning';
    const DANGER = 'danger';

    private static function notify(string $type, ?string $msg = null):array
    {
        $notify = ['type' => $type];
        if($msg != null){ $notify['msg'] = $msg;}
        return $notify;
    }

    public static function success(?string $msg = null):array
    {
       return ['notify' => self::notify(self::SUCCESS, $msg)];
    }

    public static function warning(?string $msg = null):array
    {
        return ['notify' => self::notify(self::WARNING, $msg)];
    }

    public static function error(?string $msg = null):array
    {
        return ['notify' => self::notify(self::DANGER, $msg)];
    }
}
