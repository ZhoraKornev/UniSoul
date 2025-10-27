<?php

namespace App\Telegram\Handlers;

use App\Contracts\ActionHandler;
use App\Models\BotButton;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

class BackNavigationHandler implements ActionHandler
{
    public function handle(Nutgram $bot): void
    {
        // Get the text that triggered this handler
        $text = $bot->message()?->text ?? $bot->callbackQuery()?->data;

        // Find the button that was pressed
        $currentButton = BotButton::whereHas('translations', function ($query) use ($text) {
            $query->where('value', $text);
        })->first();

        if ($currentButton && $currentButton->parent_id) {
            // Go back to parent menu
            $this->showMenu($bot, $currentButton->parent_id);
        } else {
            // Go back to main menu (start command level)
            $this->showMainMenu($bot);
        }
    }

    public function isSupport(string $actionCallbackName): bool
    {
        // Support any "back" button text
        $backTranslations = [
            __('telegram.button_back'),
            '◀️ Назад',
            '◀️ Back',
            '⬅️ Назад',
            '⬅️ Back',
        ];

        foreach ($backTranslations as $translation) {
            if (str_contains($actionCallbackName, $translation) ||
                $actionCallbackName === $translation) {
                return true;
            }
        }

        return false;
    }

    private function showMenu(Nutgram $bot, int $parentId): void
    {
        $parent = BotButton::find($parentId);

        if (!$parent) {
            $this->showMainMenu($bot);
            return;
        }

        $buttons = BotButton::where('parent_id', $parentId)
            ->orderBy('order')
            ->get();

        $keyboard = ReplyKeyboardMarkup::make(resize_keyboard: true);

        foreach ($buttons as $button) {
            $keyboard->addRow(
                KeyboardButton::make($button->getTranslation('text', app()->getLocale()))
            );
        }

        // Add back button if not at root level
        if ($parent->parent_id !== null) {
            $keyboard->addRow(
                KeyboardButton::make(__('telegram.button_back'))
            );
        }

        $bot->sendMessage(
            text: $parent->getTranslation('message', app()->getLocale())
            ?? __('telegram.select_option'),
            reply_markup: $keyboard
        );
    }

    private function showMainMenu(Nutgram $bot): void
    {
        $buttons = BotButton::whereNull('parent_id')->orderBy('order')->get();

        $keyboard = ReplyKeyboardMarkup::make(resize_keyboard: true);

        foreach ($buttons as $button) {
            $keyboard->addRow(
                KeyboardButton::make($button->getTranslation('text', app()->getLocale()))
            );
        }

        $bot->sendMessage(
            text: __('telegram.welcome_message'),
            reply_markup: $keyboard
        );
    }
}
