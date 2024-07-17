<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Common;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(?array $data = null): void
    {
        // \App\Models\User::factory(10)->create();

        if($data){
            $data['profile'] = Common::PRF_ADMIN;
            $data['modules'] = \App\Models\User::list_modules();
            $data['status']  = true;
            \App\Models\User::factory()->create($data);
        }else{
            \App\Models\User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'username' => 'test@example.com',
                'password' => Hash::make('senha123'),
                'profile' => Common::PRF_ADMIN,
                'modules' => \App\Models\User::list_modules(),
                'status' => true
            ]);
        }
    }
}
