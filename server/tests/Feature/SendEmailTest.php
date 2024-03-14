<?php

namespace Tests\Feature;

use App\Mail\Wellcome;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\TestCase;

class SendEmailTest extends TestCase
{
    public function test_email_send(): void
    {
        // Mail::fake();

        // Mail::assertNothingSent();

        // Mail::assertSent(Wellcome::class);
    }
}
