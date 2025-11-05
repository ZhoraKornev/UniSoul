<?php

namespace Database\Seeders;

use App\Models\Confession;
use App\Models\BotButton;
use App\Enums\BotCallback;
use Illuminate\Database\Seeder;

class BotButtonForConfessionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Confession::all() as $confession) {
            $this->seedForConfession($confession);
        }
    }

    private function trans(BotCallback $callback): array
    {
        $languages = config('app.supported_languages', ['en']);

        return collect($languages)->mapWithKeys(function ($lang) use ($callback) {
            return [$lang => $callback->label(locale: $lang)];
        })->toArray();
    }

    private function seedForConfession(Confession $confession): void
    {
        $confessionRootButtonId = BotButton::whereCallbackData(BotCallback::ConfessionListMenu->value)->select('id')->first()?->id;
        if (!$confessionRootButtonId) {
            return;
        }

        $confessionId = $confession->id;

        // LEVEL 1

        $learnMenuButton = BotButton::create([
            'parent_id' => $confessionRootButtonId,
            'entity_type' => Confession::class,
            'entity_id' => $confessionId,
            'text' => $this->trans(BotCallback::LearnAboutConfession),
            'callback_data' => BotCallback::LearnAboutConfession->value,
            'order' => 1,
        ]);

        // LEVEL 2

        $actions = [
            BotCallback::LearnImportantNotationAboutConfession,
            BotCallback::LearnBooksAboutConfession,
            BotCallback::LearnVideosConfession,
        ];

        foreach ($actions as $index => $action) {
            BotButton::create([
                'parent_id' => $learnMenuButton->id,
                'entity_type' => Confession::class,
                'entity_id' => $confessionId,
                'text' => $this->trans($action),
                'callback_data' => $action->value,
                'order' => $index + 1,
                'need_donations' => fake()->boolean()
            ]);
        }

        BotButton::create([
            'parent_id' => $confessionRootButtonId,
            'entity_type' => Confession::class,
            'entity_id' => $confessionId,
            'text' => $this->trans(BotCallback::ViewConfession),
            'callback_data' => BotCallback::ViewConfession->value,
            'order' => 2,
        ]);

        $menu = BotButton::create([
            'parent_id' => $confessionRootButtonId,
            'entity_type' => Confession::class,
            'entity_id' => $confessionId,
            'text' => $this->trans(BotCallback::ConfessionMenuAction),
            'callback_data' => BotCallback::ConfessionMenuAction->value,
            'order' => 3,
        ]);


        // LEVEL 2

        $actions = [
            BotCallback::Donate,
            BotCallback::PriestsList,
            BotCallback::ShowBranches,
        ];

        foreach ($actions as $index => $action) {
            BotButton::create([
                'parent_id' => $menu->id,
                'entity_type' => Confession::class,
                'entity_id' => $confessionId,
                'text' => $this->trans($action),
                'callback_data' => $action->value,
                'order' => $index + 1,
                'need_donations' => fake()->boolean()
            ]);
        }

        // Back to Main menu
        BotButton::create([
            'parent_id' => $confessionId,
            'entity_type' => Confession::class,
            'entity_id' => $confessionId,
            'text' => $this->trans(BotCallback::BackButton),
            'callback_data' => BotCallback::BackButton->value,
            'order' => 998,
        ]);
        BotButton::create([
            'parent_id' => $confessionId,
            'entity_type' => Confession::class,
            'entity_id' => $confessionId,
            'text' => $this->trans(BotCallback::MainMenu),
            'callback_data' => BotCallback::MainMenu->value,
            'order' => 998,
        ]);

        BotButton::create([
            'parent_id' => $menu->id,
            'entity_type' => Confession::class,
            'entity_id' => $confessionId,
            'text' => $this->trans(BotCallback::BackButton),
            'callback_data' => BotCallback::BackButton->value,
            'order' => 998,
        ]);

        BotButton::create([
            'parent_id' => $menu->id,
            'entity_type' => Confession::class,
            'entity_id' => $confessionId,
            'text' => $this->trans(BotCallback::MainMenu),
            'callback_data' => BotCallback::MainMenu->value,
            'order' => 999,
        ]);
    }
}
