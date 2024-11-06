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
        Organ::factory(1)->create();
        Unit::factory(4)->create();
        Sector::factory(4)->create();
        Program::factory(4)->create();
        Dotation::factory(4)->create();
        Supplier::factory(4)->create();
        Demandant::factory(4)->create();
        Ordinator::factory(4)->create();
        Comission::factory(1)->create();
        ComissionMember::factory(4)->create();
        Catalog::factory(1)->create();
        CatalogSubCategoryItem::factory(3)->create();
        CatalogItem::factory(100)->create();
        Dfd::factory(3)->create();
        DfdItem::factory(10)->create();
        Process::factory(2)->create();
    }
}
