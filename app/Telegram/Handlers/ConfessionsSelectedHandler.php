<?php

namespace App\Telegram\Handlers;

use App\Contracts\ActionHandler;
use App\Enums\BotCallback;
use App\Enums\ConfessionActions;
use App\Models\BotButton;
use App\Models\Confession;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class ConfessionsSelectedHandler implements ActionHandler
{
    public function handle(Nutgram $bot): void
    {
        $callbackData = $bot->callbackQuery()?->data;
        $confessionId = str_replace(BotCallback::ViewConfession->value . '_', '', $callbackData);
        
        $confession = Confession::find($confessionId);
        if (!$confession) {
            $bot->sendMessage(text: __('telegram.error_confession_not_found'));
            return;
        }

        $keyboard = InlineKeyboardMarkup::make();

        foreach (ConfessionActions::cases() as $action) {
            $keyboard->addRow(
                InlineKeyboardButton::make(
                    text: $this->getActionText($action),
                    callback_data: $action->value . '_' . $confessionId
                )
            );
        }

        $keyboard->addRow(
            InlineKeyboardButton::make(
                text: __('telegram.button_back'),
                callback_data: BotCallback::ConfessionListMenu->value
            ),
            InlineKeyboardButton::make(
                text: __('telegram.main_menu'),
                callback_data: BotCallback::MainMenu->value
            )
        );

        $bot->sendMessage(
            text: $confession->emoji . ' ' . $confession->getTranslation('name', app()->getLocale()),
            reply_markup: $keyboard
        );
    }

    private function getActionText(ConfessionActions $action): string
    {
        return match($action) {
            ConfessionActions::LearnAboutConfession => __('telegram.learn_about_confession'),
            ConfessionActions::ConfessionMenuAction => __('telegram.confession_menu_action'),
            ConfessionActions::ConfessionMenuSubAction => __('telegram.confession_menu_sub_action'),
        };
    }

    public function isSupport(string $actionCallbackName): bool
    {
        return str_starts_with($actionCallbackName, BotCallback::ViewConfession->value . '_');
    }
}
