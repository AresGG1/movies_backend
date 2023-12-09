<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Call other seeders from here
        $this->call([
            AdminUserSeeder::class,
            RoleSeeder::class,
        ]);
    }
}
