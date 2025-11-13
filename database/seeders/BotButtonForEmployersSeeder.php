<?php

namespace Database\Seeders;

use App\Enums\BotCallback;
use App\Models\BotButton;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class BotButtonForEmployersSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Employee::all() as $employer) {
            $this->seedForEmployers($employer);
        }
    }

    private function trans(BotCallback $callback): array
    {
        $languages = config('app.supported_languages', ['en']);

        return collect($languages)->mapWithKeys(function ($lang) use ($callback) {
            return [$lang => $callback->label(locale: $lang)];
        })->toArray();
    }

    private function seedForEmployers(Employee $employee): void
    {
        $confessionRootButtonId = BotButton::whereCallbackData(BotCallback::PriestsList->value)->select('id')->first()?->id;
        if (! $confessionRootButtonId) {
            return;
        }

        $employerID = $employee->id;

        $menu = BotButton::create([
            'parent_id' => $confessionRootButtonId,
            'entity_type' => Employee::class,
            'entity_id' => $employerID,
            'text' => $this->trans(BotCallback::ConfessionMenuAction),
            'callback_data' => BotCallback::ConfessionMenuAction->value,
            'order' => 3,
        ]);

        // LEVEL 2

        $actions = [
            BotCallback::Sorokoust,
            BotCallback::LightACandle,
            BotCallback::SubmitPrayerNote,
            BotCallback::ReadAkathists,
            BotCallback::ReadUnceasingPsalter,
            BotCallback::MemorialService,
            BotCallback::ContactEmployer,
        ];

        foreach ($actions as $index => $action) {
            BotButton::create([
                'parent_id' => $menu->id,
                'entity_type' => Employee::class,
                'entity_id' => $employerID,
                'text' => $this->trans($action),
                'callback_data' => $action->value,
                'order' => $index + 1,
                'need_donations' => fake()->boolean(),
            ]);
        }

        // Back to Main menu
        BotButton::create([
            'parent_id' => $menu->id,
            'entity_type' => Employee::class,
            'entity_id' => $employerID,
            'text' => $this->trans(BotCallback::BackButton),
            'callback_data' => BotCallback::BackButton->value,
            'order' => 998,
        ]);

        BotButton::create([
            'parent_id' => $menu->id,
            'entity_type' => Employee::class,
            'entity_id' => $employerID,
            'text' => $this->trans(BotCallback::MainMenu),
            'callback_data' => BotCallback::MainMenu->value,
            'order' => 999,
        ]);
    }
}
