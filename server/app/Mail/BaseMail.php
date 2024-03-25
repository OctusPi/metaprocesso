<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class BaseMail extends Mailable
{
    protected string $system;
    protected string $sender;
    protected string $appUrl;
    protected string $renewRoute = 'renew';

    public function __construct(){
        $this->system = env('APP_NAME','');
        $this->sender = 'Administração' . env('APP_NAME', '');
        $this->appUrl = env('CLIENT_URL', '');
    }

    protected function makeUrl(array $routes): string
    {
        return $this->appUrl.'/'.implode('/', $routes);
    }
}
