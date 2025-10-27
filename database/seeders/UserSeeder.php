<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Додавання адміністраторів
        $admins = [
            [
                'name' => 'Default Admin',
                'email' => 'default@admin.test',
                // Пароль 'password'
            ],
            [
                'name' => 'Test Admin',
                'email' => 'test@admin.test',
                // Пароль 'password'
            ],
        ];

        foreach ($admins as $adminData) {
            User::updateOrCreate(
                ['email' => $adminData['email']], // Умова пошуку
                [
                    'name' => $adminData['name'],
                    'password' => Hash::make('password'), // Шифруємо стандартний пароль
                ]
            );
        }
    }
}
