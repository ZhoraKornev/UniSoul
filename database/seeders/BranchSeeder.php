<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Confession;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supportedLanguages = ['uk', 'en', 'ro', 'de', 'ka'];
        $fakerUk = Faker::create('uk_UA');
        $fakerEn = Faker::create('en_US');
        $fakerDe = Faker::create('de_DE'); // Faker для німецької

        // 1. Перевіряємо, чи існує Confession, якщо ні, створюємо його
        $confessionId = Confession::inRandomOrder()->value('id');

        if (! $confessionId) {
            $this->call(ConfessionSeeder::class);
            $confessionId = Confession::inRandomOrder()->value('id');
            if (! $confessionId) {
                throw new \Exception('Cannot find or create a Confession.');
            }
        }

        // 2. Генеруємо випадкову кількість філій (від 5 до 10)
        $numberOfBranches = rand(5, 10);

        for ($i = 0; $i < $numberOfBranches; $i++) {

            $name = [];
            $address = [];
            $description = [];

            // --- Генерація базових даних для різних локалей ---

            // UK
            $baseCityUk = $fakerUk->city;
            $baseAddressUk = "м. {$baseCityUk}, ".$fakerUk->streetAddress();
            $baseDescUk = $fakerUk->sentence(8);

            // EN
            $baseCityEn = $fakerEn->city;
            $baseAddressEn = $fakerEn->streetAddress().", {$baseCityEn}";
            $baseDescEn = $fakerEn->sentence(8);

            // DE
            $baseCityDe = $fakerDe->city;
            $baseAddressDe = $fakerDe->streetAddress().", {$baseCityDe}";
            $baseDescDe = $fakerDe->sentence(8);

            // --- Заповнення багатомовних полів ---
            foreach ($supportedLanguages as $lang) {
                if ($lang === 'uk') {
                    $name['uk'] = $baseCityUk.' Філія '.($i + 1);
                    $address['uk'] = $baseAddressUk;
                    $description['uk'] = $baseDescUk;
                } elseif ($lang === 'en') {
                    $name['en'] = $baseCityEn.' Branch '.($i + 1);
                    $address['en'] = $baseAddressEn;
                    $description['en'] = $baseDescEn;
                } elseif ($lang === 'de') {
                    $name['de'] = $baseCityDe.' Zweigstelle '.($i + 1);
                    $address['de'] = $baseAddressDe;
                    $description['de'] = $baseDescDe;
                } else {
                    // Заглушка для RO та KA (використовуємо англійську базу)
                    $name[$lang] = $baseCityEn.' Branch '.($i + 1)." ($lang)";
                    $address[$lang] = $baseAddressEn." ($lang)";
                    $description[$lang] = $baseDescEn." ($lang)";
                }
            }

            // --- Створення запису ---
            Branch::create([
                'confession_id' => $confessionId,
                'name' => $name,
                'address' => $address,
                'description' => $description,
                'phone' => $fakerEn->phoneNumber,
                'email' => $fakerEn->unique()->safeEmail,
                'schedule' => [
                    'Mon-Fri' => '9:00-18:00',
                    'Sat' => $fakerEn->randomElement(['9:00-14:00', 'Closed']),
                    'Sun' => 'Closed',
                ],
                // Генеруємо випадкові координати в межах Європи
                'latitude' => $fakerEn->latitude(46, 52),
                'longitude' => $fakerEn->longitude(24, 40),
                'active' => $fakerEn->boolean(90),
            ]);
        }
    }
}
