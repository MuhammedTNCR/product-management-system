<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category_factory = new CategoryFactory();
        $category_factory->count(10)->create();
    }
}
