<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AuthorSeeder::class);
        $this->call(SubjectSeeder::class);
        $this->call(BookSeeder::class);
        $this->call(UserSeeder::class);
    }
}
