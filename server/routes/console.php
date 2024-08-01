<?php

use App\Models\Common;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('make:bigboss', function () {
    
        $this->info('Create a super User');

        $data = [];
        $data['name']     = $this->ask('Type username:');
        $data['email']    = $this->ask('Type email:');
        $data['password'] = $this->ask('Type password:');
        $data['username'] = $data['email'];
        $data['password'] = Hash::make($data['password']);
        $data['profile'] = Common::PRF_ADMIN;
        $data['modules'] = \App\Models\User::list_modules();
        $data['status']  = true;
        
        \App\Models\User::factory()->create($data);
        $this->info('Sussefully!!!');
    
    
})->purpose('Create a default super user to access system');
