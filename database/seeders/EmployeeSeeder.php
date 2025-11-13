<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Employee;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Список усіх підтримуваних мов для Translatable полів
        $supportedLanguages = ['uk', 'en', 'ro', 'de', 'ka'];

        // Ініціалізація Faker для реалістичних даних
        $fakerUk = Faker::create('uk_UA');
        $fakerEn = Faker::create('en_US');
        $fakerDe = Faker::create('de_DE'); // Додаємо Faker для німецької

        // Список основних позицій для генерації
        $positions_en = ['Software Engineer', 'Project Manager', 'Data Analyst', 'HR Specialist', 'QA Tester', 'UX Designer', 'Team Lead', 'Accountant', 'Sales Representative', 'Customer Support'];
        $positions_uk = ['Інженер-програміст', 'Керівник проєктів', 'Аналітик даних', 'Спеціаліст з кадрів', 'Тестувальник ПЗ', 'UX Дизайнер', 'Тімлід', 'Бухгалтер', 'Торговий представник', 'Служба підтримки'];

        // Мануальні переклади основних позицій для DE (Німеччина)
        $positions_de = ['Softwareentwickler', 'Projektmanager', 'Datenanalyst', 'HR-Spezialist', 'QA-Tester', 'UX-Designer', 'Teamleiter', 'Buchhalter', 'Vertriebsmitarbeiter', 'Kundenbetreuung'];

        // 1. Отримуємо всі філії.
        $branches = Branch::all();

        if ($branches->isEmpty()) {
            $this->call(BranchSeeder::class);
            $branches = Branch::all();
            if ($branches->isEmpty()) {
                throw new \Exception('Cannot find or create a Branch. Ensure ConfessionSeeder is run first.');
            }
        }

        // 2. Перебираємо кожну філію та створюємо для неї співробітників
        foreach ($branches as $branch) {
            // Генеруємо випадкову кількість співробітників (від 15 до 50)
            $count = rand(15, 50);

            for ($i = 0; $i < $count; $i++) {
                $sex = $fakerUk->randomElement(['male', 'female']);

                // --- Генерація імен (окремо для UK, EN, DE) ---
                $firstNameEn = $fakerEn->firstName($sex);
                $lastNameEn = $fakerEn->lastName();
                $firstNameUk = $fakerUk->firstName($sex);
                $lastNameUk = $fakerUk->lastName();
                $firstNameDe = $fakerDe->firstName($sex);
                $lastNameDe = $fakerDe->lastName();

                $name = [];
                $position = [];
                $address = [];

                $positionIndex = rand(0, count($positions_en) - 1);
                $isAvailable = $fakerUk->boolean(85); // 85% шанс бути доступним

                // --- Заповнення багатомовних полів ---
                foreach ($supportedLanguages as $lang) {
                    if ($lang === 'en') {
                        $name['en'] = "{$firstNameEn} {$lastNameEn}";
                        $position['en'] = $positions_en[$positionIndex];
                        $address['en'] = $fakerEn->streetAddress().', Office '.rand(100, 999);
                    } elseif ($lang === 'uk') {
                        $name['uk'] = "{$firstNameUk} {$lastNameUk}";
                        $position['uk'] = $positions_uk[$positionIndex];
                        $address['uk'] = $fakerUk->address();
                    } elseif ($lang === 'de') {
                        // Використовуємо реалістичні німецькі дані
                        $name['de'] = "{$firstNameDe} {$lastNameDe}";
                        $position['de'] = $positions_de[$positionIndex];
                        $address['de'] = $fakerDe->streetAddress().', Büro '.rand(100, 999);
                    } else {
                        // Для RO та KA використовуємо EN як заглушку
                        $name[$lang] = "{$firstNameEn} {$lastNameEn} ($lang)";
                        $position[$lang] = $positions_en[$positionIndex]." ($lang)";
                        $address[$lang] = $fakerEn->streetAddress()." ($lang)";
                    }
                }

                $employeeData = [
                    'branch_id' => $branch->id,
                    'name' => $name,
                    'position' => $position,
                    'sex' => $sex,
                    'age' => rand(22, 60),
                    'is_available' => $isAvailable,
                    'phone' => $fakerUk->unique()->e164PhoneNumber,
                    'telegram_nickname' => '@'.strtolower($fakerUk->unique()->userName),
                    'address' => $address,
                    'other_info' => $isAvailable ? null : [
                        // Використовуємо українську причину для прикладу
                        'reason' => $fakerUk->randomElement(['Відпустка', 'Лікарняний', 'Навчання', 'Відрядження']),
                    ],
                ];

                Employee::create($employeeData);
            }
        }
    }
}
