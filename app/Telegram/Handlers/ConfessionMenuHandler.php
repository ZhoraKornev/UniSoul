<?php

namespace App\Telegram\Handlers;

use App\Contracts\ActionHandler;
use App\Enums\BotCallback;
use App\Models\BotButton;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

class ConfessionMenuHandler implements ActionHandler
{
    public function handle(Nutgram $bot): void
    {
        $confessionButton = BotButton::where('callback_data', 'confession_menu')->first();
        $buttons = BotButton::where('parent_id', $confessionButton->id)->orderBy('order')->get();

        $keyboard = ReplyKeyboardMarkup::make(resize_keyboard: true);

        foreach ($buttons as $button) {
            $keyboard->addRow(
                KeyboardButton::make($button->getTranslation('text', app()->getLocale()))
            );
        }

        $bot->sendMessage(
            text: trans('telegram.confession_menu'),
            reply_markup: $keyboard
        );
    }

    public function isSupport(string $actionCallbackName): bool
    {
        // Support both callback data and text-based triggers
        return $actionCallbackName === BotCallback::ViewConfessions->value
            || $actionCallbackName === BotCallback::ViewConfessions->getDisplayText()
            || str_contains($actionCallbackName, '📖');    }
}
