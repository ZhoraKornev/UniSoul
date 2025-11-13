<?php

namespace Database\Seeders;

use App\Enums\BotCallback;
use App\Models\BotButton;
use Illuminate\Database\Seeder;

class BotButtonSeeder extends Seeder
{
    public function run(): void
    {
        $this->createButton(null, BotCallback::ConfessionListMenu, 1);
    }

    private function createButton(?int $parentId, BotCallback $callback, int $order): void
    {
        BotButton::create([
            'parent_id' => $parentId,
            'entity_type' => null,
            'entity_id' => null,
            'text' => $this->translations($callback),
            'callback_data' => $callback->value,
            'order' => $order,
        ]);
    }

    private function translations(BotCallback $callback): array
    {
        $languages = config('app.supported_languages', ['en']);

        return collect($languages)
            ->mapWithKeys(fn (string $lang) => [$lang => $callback->label($lang)])
            ->toArray();
    }
}
