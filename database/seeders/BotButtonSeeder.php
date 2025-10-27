<?php

namespace Database\Seeders;

use App\Models\BotButton;
use Illuminate\Database\Seeder;

class BotButtonSeeder extends Seeder
{
    public function run(): void
    {
        // Main menu buttons (parent_id = null)
        $confessionButton = BotButton::create([
            'parent_id' => null,
            'text' => [
                'uk' => '🙏 Вибір конфесії',
                'en' => '🙏 Select Confession',
                'de' => '🙏 Konfessionsauswahl',
                'ro' => '🙏 Selectarea Confesiunii',
                'ka' => '🙏 კონფესიის არჩევა'
            ],
            'callback_data' => 'confession_menu',
            'order' => 1,
        ]);

        BotButton::create([
            'parent_id' => null,
            'text' => [
                'uk' => '❓ Допомога',
                'en' => '❓ Help',
                'de' => '❓ Hilfe',
                'ro' => '❓ Ajutor',
                'ka' => '❓ დახმარება'
            ],
            'callback_data' => 'help_menu',
            'order' => 2,
        ]);

        BotButton::create([
            'parent_id' => null,
            'text' => [
                'uk' => '⚙️ Налаштування',
                'en' => '⚙️ Settings',
                'de' => '⚙️ Einstellungen',
                'ro' => '⚙️ Setări',
                'ka' => '⚙️ პარამეტრები'
            ],
            'callback_data' => 'settings_menu',
            'order' => 3,
        ]);

        // Confession submenu buttons (parent_id = confession button id)
        BotButton::create([
            'parent_id' => $confessionButton->id,
            'text' => [
                'uk' => '📖 Переглянути конфесії',
                'en' => '📖 View Confessions',
                'de' => '📖 Konfessionen anzeigen',
                'ro' => '📖 Vizualizare Confesiuni',
                'ka' => '📖 კონფესიების ნახვა'
            ],
            'callback_data' => 'view_confessions',
            'order' => 1,
        ]);

        BotButton::create([
            'parent_id' => $confessionButton->id,
            'text' => [
                'uk' => '⬅️ Назад',
                'en' => '⬅️ Back',
                'de' => '⬅️ Zurück',
                'ro' => '⬅️ Înapoi',
                'ka' => '⬅️ უკან'
            ],
            'callback_data' => 'back_to_main',
            'order' => 2,
        ]);

        $settingsParent = BotButton::where('callback_data', 'settings_menu')->first();

        if ($settingsParent) {
            $settingsButtons = [
                [
                    'order' => 1,
                    'callback_data' => 'set_lang',
                    'text' => ['uk' => 'Змінити мову 🌐', 'en' => 'Change Language 🌐', 'ro' => 'Schimbă Limba 🌐', 'de' => 'Sprache ändern 🌐', 'ka' => 'ენის შეცვლა 🌐'],
                ],
                [
                    'order' => 2,
                    'callback_data' => 'set_gender',
                    'text' => ['uk' => 'Вказати стать 🚻', 'en' => 'Specify Gender 🚻', 'ro' => 'Specifică Genul 🚻', 'de' => 'Geschlecht angeben 🚻', 'ka' => 'სქესის მითითება 🚻'],
                ],
                [
                    'order' => 3,
                    'callback_data' => 'set_country',
                    'text' => ['uk' => 'Змінити країну 🗺️', 'en' => 'Change Country 🗺️', 'ro' => 'Schimbă Țara 🗺️', 'de' => 'Land ändern 🗺️', 'ka' => 'ქვეყნის შეცვლა 🗺️'],
                ],
                [
                    'order' => 4,
                    'callback_data' => 'main_menu',
                    'text' => ['uk' => '🔙 Головне меню', 'en' => '🔙 Main Menu', 'ro' => '🔙 Meniul Principal', 'de' => '🔙 Hauptmenü', 'ka' => '🔙 მთავარი მენიუ'],
                ],
            ];

            foreach ($settingsButtons as $buttonData) {
                BotButton::create([
                    'parent_id' => $settingsParent->id,
                    'text' => $buttonData['text'],
                    'callback_data' => $buttonData['callback_data'],
                    'order' => $buttonData['order'],
                ]);
            }
        }
    }
}