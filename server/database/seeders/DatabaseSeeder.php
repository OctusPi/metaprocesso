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
        Unit::factory(1)->create();
        Sector::factory(1)->create();
        Program::factory(1)->create();
        Dotation::factory(1)->create();
        Supplier::factory(1)->create();
        Demandant::factory(1)->create();
        Ordinator::factory(1)->create();
        Comission::factory(1)->create();
        ComissionMember::factory(1)->create();
        Catalog::factory(1)->create();
        CatalogSubCategoryItem::factory(1)->create();
        CatalogItem::factory(1)->create();
        Dfd::factory(1)->create();
        DfdItem::factory(1)->create();
        Process::factory(1)->create();
    }
}
