<?php

namespace App\Telegram\Handlers;

use App\Contracts\ActionHandler;
use App\Models\BotButton;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

class DynamicMenuHandler implements ActionHandler
{
    public function handle(Nutgram $bot): void
    {
        $text = $bot->message()?->text ?? $bot->callbackQuery()?->data;

        // Find the button by translated text
        $button = BotButton::whereHas('translations', function ($query) use ($text) {
            $query->where('value', $text)
                ->orWhere('value', 'like', '%' . trim(preg_replace('/^[^\s]+\s/', '', $text)) . '%');
        })->first();

        if (!$button) {
            $bot->sendMessage(text: __('telegram.unknown_command'));
            return;
        }

        // If button has an action/handler, delegate to specific handler
        if ($button->action) {
            $this->handleButtonAction($bot, $button);
            return;
        }

        // If button has children, show submenu
        $children = BotButton::where('parent_id', $button->id)
            ->orderBy('order')
            ->get();

        if ($children->isNotEmpty()) {
            $this->showSubmenu($bot, $button, $children);
            return;
        }

        // Leaf node with no action - show message only
        $bot->sendMessage(
            text: $button->getTranslation('message', app()->getLocale())
            ?? __('telegram.no_action_available')
        );
    }

    public function isSupport(string $actionCallbackName): bool
    {
        // Skip back buttons - they have their own handler
        if (str_contains($actionCallbackName, __('telegram.button_back'))) {
            return false;
        }

        // Check if any button matches this text
        return BotButton::whereHas('translations', function ($query) use ($actionCallbackName) {
            $query->where('value', $actionCallbackName)
                ->orWhere('value', 'like', '%' . trim(preg_replace('/^[^\s]+\s/', '', $actionCallbackName)) . '%');
        })->exists();
    }

    private function showSubmenu(Nutgram $bot, BotButton $parent, $children): void
    {
        $keyboard = ReplyKeyboardMarkup::make(resize_keyboard: true);

        foreach ($children as $child) {
            $keyboard->addRow(
                KeyboardButton::make($child->getTranslation('text', app()->getLocale()))
            );
        }

        // Add back button
        $keyboard->addRow(
            KeyboardButton::make(__('telegram.button_back'))
        );

        $bot->sendMessage(
            text: $parent->getTranslation('message', app()->getLocale())
            ?? __('telegram.select_option'),
            reply_markup: $keyboard
        );
    }

    private function handleButtonAction(Nutgram $bot, BotButton $button): void
    {
        // Delegate to specific handler based on action
        try {
            $provider = app(\App\Services\HandlerProvider::class);
            $handler = $provider->provide($button->action);
            $handler->handle($bot);
        } catch (\RuntimeException $e) {
            logger()->warning('Button action handler not found', [
                'action' => $button->action,
                'button_id' => $button->id,
            ]);

            $bot->sendMessage(
                text: $button->getTranslation('message', app()->getLocale())
                ?? __('telegram.action_not_available')
            );
        }
    }
}
