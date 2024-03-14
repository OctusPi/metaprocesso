<?php

namespace Tests\Unit;

use App\Mail\Wellcome;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\TestCase;

class SendEmailTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_email_send(): void
    {
        // Mail::fake();

        // Mail::assertNothingSent();

        // Mail::assertSent(Wellcome::class);
    }
}
