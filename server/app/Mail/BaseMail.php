<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class BaseMail extends Mailable
{
    private string $system = env('APP_NAME', '');
    private string $sender = 'Administração' . env('APP_NAME', '');
    private string $appUrl = env('APP_URL', '');
    private string $renewRoute = 'renew';

    private function makeUrl(string ...$routes): string
    {
        return join([$this->appUrl, ...$routes], '/');
    }
}
