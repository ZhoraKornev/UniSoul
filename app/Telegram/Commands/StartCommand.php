<?php

namespace App\Telegram\Commands;

use App\Models\BotButton;
use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

class StartCommand extends Command
{
    protected string $command = 'start';

    protected ?string $description = 'A lovely start command';

    public function handle(Nutgram $bot): void
    {
        $buttons = BotButton::whereNull('parent_id')->orderBy('order')->get();
        
        $keyboard = ReplyKeyboardMarkup::make(resize_keyboard: true);
        
        foreach ($buttons as $button) {
            $keyboard->addRow(
                KeyboardButton::make($button->getTranslation('text', app()->getLocale()))
            );
        }

        $bot->sendMessage(
            text: trans('telegram.welcome_message'),
            reply_markup: $keyboard
        );
    }
}
