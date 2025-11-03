<?php

namespace Database\Seeders;

use App\Models\Confession;
use App\Models\BotButton;
use App\Enums\BotCallback;
use Illuminate\Database\Seeder;

class ConfessionBotButtonSeeder extends Seeder
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
        if (! $confessionRootButtonId) {
            return;
        }

        $confessionId = $confession->id;

        // LEVEL 1

        BotButton::create([
            'parent_id'     => $confessionRootButtonId,
            'entity_type'   => Confession::class,
            'entity_id'     => $confessionId,
            'text'          => $this->trans(BotCallback::LearnAboutConfession),
            'callback_data' => BotCallback::LearnAboutConfession->value,
            'order'         => 1,
        ]);

        BotButton::create([
            'parent_id'     => $confessionRootButtonId,
            'entity_type'   => Confession::class,
            'entity_id'     => $confessionId,
            'text'          => $this->trans(BotCallback::ViewConfession),
            'callback_data' => BotCallback::ViewConfession->value,
            'order'         => 2,
        ]);

        $menu = BotButton::create([
            'parent_id'     => $confessionRootButtonId,
            'entity_type'   => Confession::class,
            'entity_id'     => $confessionId,
            'text'          => $this->trans(BotCallback::ConfessionMenuAction),
            'callback_data' => BotCallback::ConfessionMenuAction->value,
            'order'         => 3,
        ]);

        // LEVEL 2

        $actions = [
            BotCallback::Sorokoust,
            BotCallback::LightACandle,
            BotCallback::SubmitPrayerNote,
            BotCallback::ReadAkathists,
            BotCallback::ReadUnceasingPsalter,
            BotCallback::MemorialService,
            BotCallback::Donate,
            BotCallback::PriestsList,
        ];

        foreach ($actions as $index => $action) {
            BotButton::create([
                'parent_id'     => $menu->id,
                'entity_type'   => Confession::class,
                'entity_id'     => $confessionId,
                'text'          => $this->trans($action),
                'callback_data' => $action->value,
                'order'         => $index + 1,
            ]);
        }

        // Back to Main menu

        BotButton::create([
            'parent_id'     => $menu->id,
            'entity_type'   => Confession::class,
            'entity_id'     => $confessionId,
            'text'          => $this->trans(BotCallback::MainMenu),
            'callback_data' => BotCallback::MainMenu->value,
            'order'         => 999,
        ]);
    }
}
