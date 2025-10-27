<?php

namespace App\Telegram\Handlers;

use App\Contracts\ActionHandler;
use App\Enums\BotCallback;
use App\Models\Confession;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

class ConfessionSelectHandler implements ActionHandler
{
    public function handle(Nutgram $bot): void
    {
        $confessions = Confession::query()
            ->where('active', true)
            ->get();

        // Use inline keyboard for callback-based navigation
        $keyboard = InlineKeyboardMarkup::make();

        foreach ($confessions as $confession) {
            $keyboard->addRow(
                InlineKeyboardButton::make(
                    text: $confession->emoji . ' ' . $confession->getTranslation('name', app()->getLocale()),
                    callback_data: BotCallback::SelectConfession->value . ':' . $confession->id
                )
            );
        }

        $keyboard->addRow(
            InlineKeyboardButton::make(
                text: __('telegram.button_back'),
                callback_data: BotCallback::BackToStart->value
            )
        );

        $bot->sendMessage(
            text: __('telegram.select_confession'),
            reply_markup: $keyboard
        );
    }

    public function isSupport(string $actionCallbackName): bool
    {
        // Support both callback data and text-based triggers
        return $actionCallbackName === BotCallback::ConfessionMenu->value
            || $actionCallbackName === BotCallback::ConfessionMenu->getDisplayText()
            || str_contains($actionCallbackName, '📖');
    }
}
