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
        $this->call([
            ConfessionSeeder::class, // Додає країни та конфесії
            UserSeeder::class,       // Додає адміністраторів
            BotButtonSeeder::class,       // Додає адміністраторів
        ]);
    }
}
