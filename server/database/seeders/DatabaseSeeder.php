<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Common;
use App\Models\Dfd;
use App\Models\Dotation;
use App\Models\Organ;
use App\Models\Program;
use App\Models\Sector;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(?array $data = null): void
    {
        Organ::factory(5)->create();
        Unit::factory(5)->create();
        Sector::factory(5)->create();
        Program::factory(5)->create();
        Dotation::factory(5)->create();
        Supplier::factory(5)->create();
    }
}
