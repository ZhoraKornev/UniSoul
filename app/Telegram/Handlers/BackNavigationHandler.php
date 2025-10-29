<?php

namespace App\Telegram\Handlers;

use App\Contracts\ActionHandler;
use App\Enums\BotCallback;
use App\Models\BotButton;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class BackNavigationHandler implements ActionHandler
{
    public function handle(Nutgram $bot): void
    {
        $callbackData = $bot->callbackQuery()?->data;

        if ($callbackData === 'confession_menu') {
            $this->showConfessionMenu($bot);
        } else {
            $this->showMainMenu($bot);
        }
    }

    public function isSupport(string $actionCallbackName): bool
    {
        return $actionCallbackName === BotCallback::BackButton->value ||
               $actionCallbackName === 'confession_menu';
    }

    private function showConfessionMenu(Nutgram $bot): void
    {
        $confessionButton = BotButton::where('callback_data', 'confession_menu')->first();
        $buttons = BotButton::where('parent_id', $confessionButton->id)->orderBy('order')->get();

        $keyboard = InlineKeyboardMarkup::make();

        foreach ($buttons as $button) {
            /** @var BotButton $button */
            $keyboard->addRow(
                InlineKeyboardButton::make(
                    text: $button->getTranslation('text', app()->getLocale()),
                    callback_data: $button->callback_data
                )
            );
        }

        $bot->sendMessage(
            text: __('telegram.confession_menu'),
            reply_markup: $keyboard
        );
    }

    private function showMainMenu(Nutgram $bot): void
    {
        $buttons = BotButton::whereNull('parent_id')->orderBy('order')->get();

        $keyboard = InlineKeyboardMarkup::make();

        foreach ($buttons as $button) {
            /** @var BotButton $button */
            $keyboard->addRow(
                InlineKeyboardButton::make(
                    text: $button->getTranslation('text', app()->getLocale()),
                    callback_data: $button->callback_data
                )
            );
        }

        $bot->sendMessage(
            text: __('telegram.welcome_message'),
            reply_markup: $keyboard
        );
    }
}
