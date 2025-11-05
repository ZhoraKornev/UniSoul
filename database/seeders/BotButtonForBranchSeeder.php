<?php

namespace Database\Seeders;

use App\Models\BotButton;
use App\Enums\BotCallback;
use App\Models\Branch;
use Illuminate\Database\Seeder;

class BotButtonForBranchSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Branch::all() as $branch) {
            $this->seedForBranch($branch);
        }
    }

    private function trans(BotCallback $callback): array
    {
        $languages = config('app.supported_languages', ['en']);

        return collect($languages)->mapWithKeys(function ($lang) use ($callback) {
            return [$lang => $callback->label(locale: $lang)];
        })->toArray();
    }

    private function seedForBranch(Branch $branch): void
    {
        $confessionRootButtonId = BotButton::whereCallbackData(BotCallback::ShowBranches->value)->select('id')->first()?->id;
        if (!$confessionRootButtonId) {
            return;
        }

        $branchID = $branch->id;

        $actions = [
            BotCallback::PriestsList,
            BotCallback::Donate,
            BotCallback::ContactEmployer,
        ];

        foreach ($actions as $index => $action) {
            BotButton::create([
                'parent_id' => $confessionRootButtonId,
                'entity_type' => Branch::class,
                'entity_id' => $branchID,
                'text' => $this->trans($action),
                'callback_data' => $action->value,
                'order' => $index + 1,
                'need_donations' => fake()->boolean()
            ]);
        }
    }
}
