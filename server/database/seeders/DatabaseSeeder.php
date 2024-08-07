<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Dfd;
use App\Models\Unit;
use App\Models\User;
use App\Models\Organ;
use App\Models\Sector;
use App\Models\Catalog;
use App\Models\DfdItem;
use App\Models\Process;
use App\Models\Program;
use App\Models\Dotation;
use App\Models\Supplier;
use App\Models\Comission;
use App\Models\Demandant;
use App\Models\Ordinator;
use App\Models\CatalogItem;
use App\Models\ComissionMember;
use Illuminate\Database\Seeder;
use App\Models\CatalogSubCategoryItem;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(?array $data = null): void
    {
        // User::factory(20)->create();
        // Organ::factory(30)->create();
        // Unit::factory(200)->create();
        // Sector::factory(150)->create();
        // Program::factory(100)->create();
        // Dotation::factory(100)->create();
        // Supplier::factory(100)->create();
        // Demandant::factory(100)->create();
        // Ordinator::factory(100)->create();
        // Comission::factory(100)->create();
        // ComissionMember::factory(300)->create();
        // Catalog::factory(50)->create();
        // CatalogSubCategoryItem::factory(50)->create();
        // CatalogItem::factory(200)->create();
        // Dfd::factory(100)->create();
        // DfdItem::factory(1000)->create();
        Process::factory(10)->create();
    }
}
