<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class BaseMail extends Mailable
{
    private string $system;
    private string $sender;
    private string $appUrl;
    private string $renewRoute = 'renew';

    public function __construct(){
        $this->system = env('APP_NAME','');
        $this->sender = 'Administração' . env('APP_NAME', '');
        $this->appUrl = env('CLIENT_URL', '');
    }

    private function makeUrl(string ...$routes): string
    {
        return join([$this->appUrl, ...$routes], '/');
    }
}
