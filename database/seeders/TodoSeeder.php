<?php

namespace Database\Seeders;

use App\Models\Todo;
use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    public function run()
    {
        \App\Models\Todo::factory()->count(10)->create();
    }
}