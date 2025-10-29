<?php

namespace Database\Seeders;

use App\Enums\BotCallback;
use App\Enums\ConfessionActions;
use App\Enums\ConfessionSubActions;
use App\Models\BotButton;
use Illuminate\Database\Seeder;

class BotButtonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define a placeholder Confession ID for demonstration purposes.
        // The ID is kept in callbacks to maintain context for future handlers.
        $mockConfessionId = 1;

        // -----------------------------------------------------------------
        // LEVEL 0: MAIN MENU BUTTONS (parent_id = null)
        // -----------------------------------------------------------------

        $confessionListButton = BotButton::create([
            'parent_id' => null,
            'text' => [
                'uk' => '🙏 Вибір конфесії',
                'en' => '🙏 Select Confession',
                'de' => '🙏 Konfessionsauswahl',
                'ro' => '🙏 Selectarea Confesiunii',
                'ka' => '🙏 კონფესიის არჩევა'
            ],
            // Callback to initiate the Confession Actions flow (now Level 1 parent)
            'callback_data' => BotCallback::ConfessionListMenu->value,
            'order' => 1,
        ]);

        // -----------------------------------------------------------------
        // LEVEL 1: CONFESSION ACTIONS (parent_id = confessionListButton->id)
        // Actions shown immediately after clicking the Main Menu 'Confession' button
        // -----------------------------------------------------------------

        // Action 1: Learn about the confession
        BotButton::create([
            'parent_id' => $confessionListButton->id, // Parent is the main 'Confession List' button
            'text' => ['uk' => 'ℹ️ Дізнатися більше', 'en' => 'ℹ️ Learn More', 'de' => 'ℹ️ Mehr erfahren', 'ro' => 'ℹ️ Află mai multe', 'ka' => 'ℹ️ მეტის გაგება'],
            'callback_data' => ConfessionActions::LearnAboutConfession->value . ':' . $mockConfessionId,
            'order' => 1,
        ]);

        // Action 2: Menu for Liturgical Services (Parent for Level 2)
        $confessionMenuActionButton = BotButton::create([
            'parent_id' => $confessionListButton->id, // Parent is the main 'Confession List' button
            'text' => ['uk' => '🙏 Замовити Служби', 'en' => '🙏 Order Services', 'de' => '🙏 Gottesdienste bestellen', 'ro' => '🙏 Comandă Servicii', 'ka' => '🙏 მომსახურების შეკვეთა'],
            'callback_data' => ConfessionActions::ConfessionMenuAction->value . ':' . $mockConfessionId,
            'order' => 2,
        ]);

        // Back button for Level 1 (Actions Menu)
        BotButton::create([
            'parent_id' => $confessionListButton->id,
            'text' => ['uk' => '⬅️ Головне меню', 'en' => '⬅️ Main Menu', 'de' => '⬅️ Hauptmenü', 'ro' => '⬅️ Meniul Principal', 'ka' => '⬅️ მთავარი мeню'],
            'callback_data' => BotCallback::MainMenu->value,
            'order' => 99,
        ]);

        // -----------------------------------------------------------------
        // LEVEL 2: CONFESSION SUB ACTIONS (parent_id = confessionMenuActionButton->id)
        // Liturgical Services menu
        // -----------------------------------------------------------------

        $subActions = [
            ['enum' => ConfessionSubActions::Sorokoust, 'uk' => 'Сорокоуст', 'en' => 'Sorokoust'],
            ['enum' => ConfessionSubActions::LightACandle, 'uk' => 'Поставити свічку', 'en' => 'Light a Candle'],
            ['enum' => ConfessionSubActions::SubmitPrayerNote, 'uk' => 'Подати записку', 'en' => 'Submit Prayer Note'],
            ['enum' => ConfessionSubActions::ReadAkathists, 'uk' => 'Читання Акафістів', 'en' => 'Reading of Akathists'],
            ['enum' => ConfessionSubActions::ReadUnceasingPsalter, 'uk' => 'Читання Неусипаної Псалтирі', 'en' => 'Reading of Unceasing Psalter'],
            ['enum' => ConfessionSubActions::MemorialService, 'uk' => 'Панахида', 'en' => 'Memorial Service'],
        ];

        foreach ($subActions as $index => $action) {
            BotButton::create([
                'parent_id' => $confessionMenuActionButton->id,
                'text' => [
                    'uk' => $action['uk'],
                    'en' => $action['en'],
                    'de' => $action['en'], // Placeholder
                    'ro' => $action['en'], // Placeholder
                    'ka' => $action['en'], // Placeholder
                ],
                // Callback includes sub-action and Confession ID
                'callback_data' => $action['enum']->value . ':' . $mockConfessionId,
                'order' => $index + 1,
            ]);
        }

        // Back button for Level 2 (Sub Actions Menu)
        BotButton::create([
            'parent_id' => $confessionMenuActionButton->id,
            'text' => ['uk' => '⬅️ Назад до Дій', 'en' => '⬅️ Back to Actions', 'de' => '⬅️ Zurück zu Aktionen', 'ro' => '⬅️ Înapoi la Acțiuni', 'ka' => '⬅️ უკან мооქმედებებში'],
            'callback_data' => BotCallback::ConfessionListMenu->value, // Returns to Level 1 Actions view
            'order' => 99,
        ]);
    }
}
