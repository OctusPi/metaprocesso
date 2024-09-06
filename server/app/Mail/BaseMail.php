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
        $this->system = config('app.name');
        $this->sender = 'Administração ' . config('app.name');
        $this->appUrl = config('app.url');
    }

    protected function makeUrl(array $routes): string
    {
        return $this->appUrl.'/'.implode('/', $routes);
    }
}
