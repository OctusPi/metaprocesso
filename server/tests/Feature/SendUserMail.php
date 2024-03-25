<?php

namespace Tests\Feature;

use App\Mail\ChangePassNotification;
use App\Mail\ChangePassRequest;
use App\Mail\Wellcome;
use App\Models\User;
use App\Utils\Dates;
use Tests\TestCase;
use Mail;

class SendUserMail extends TestCase
{
    public function test_wellcome(): void
    {
        $user = User::factory()->create();
        $msgSent = Mail::to($user)->send(new Wellcome($user));

        $this->assertNotNull($msgSent);
    }

    public function test_user_pass_change_request(): void {
        $user = User::factory()->create(['password' => 'Test123']);
        $msgSent = Mail::to($user)->send(
            new ChangePassRequest($user, Dates::futurePTBR(1))
        );

        $this->assertNotNull($msgSent);
    }
    
    public function test_user_pass_change_notify(): void {
        $user = User::factory()->create(['password' => 'Test123']);
        $msgSent = Mail::to($user)->send(
            new ChangePassNotification()
        );

        $this->assertNotNull($msgSent);
    } 
}
