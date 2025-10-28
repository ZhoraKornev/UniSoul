<?php

namespace App\Telegram\Commands;

use App\Models\BotButton;
use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton; // Використовуємо Inline
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup; // Використовуємо Inline

class StartCommand extends Command
{
    protected string $command = 'start';

    protected ?string $description = 'A lovely start command';

    public function handle(Nutgram $bot): void
    {
        $buttons = BotButton::whereNull('parent_id')->orderBy('order')->get();

        $keyboard = InlineKeyboardMarkup::make();

        foreach ($buttons as $button) {
            $keyboard->addRow(
                InlineKeyboardButton::make(
                    text: $button->getTranslation('text', app()->getLocale()),
                    callback_data: $button->callback_data
                )
            );
        }

        $bot->sendMessage(
            text: trans('telegram.welcome_message'),
            reply_markup: $keyboard
        );
    }
}
