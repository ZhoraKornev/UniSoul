<?php

namespace App\Telegram\Conversations;

use App\Models\BotButton;
use App\Models\Confession;
use App\Enums\ConfessionActions;
use SergiX44\Nutgram\Conversations\InlineMenu;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

class ConfessionConversation extends InlineMenu
{
    public function start(Nutgram $bot): void
    {
        $confessions = Confession::where('active', true)->get();

        $this->clearButtons();
        $this->menuText(__('telegram.select_confession'));

        foreach ($confessions as $confession) {
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $confession->emoji . ' ' . $confession->getTranslation('name', app()->getLocale()),
                    callback_data: 'confession:' . $confession->id . '@showConfession'
                )
            );
        }

        $this->addButtonRow(
            InlineKeyboardButton::make(
                text: __('telegram.button_back'),
                callback_data: 'confession_menu@handleConfessionMenu'
            ),
            InlineKeyboardButton::make(
                text: __('telegram.main_menu'),
                callback_data: 'main_menu@backToMain'
            )
        );

        $this->showMenu();
    }

    public function handleConfessionMenu(Nutgram $bot): void
    {
        $confessionButton = BotButton::where('callback_data', 'confession_menu')->first();

        if (!$confessionButton) {
            $this->start($bot);
            return;
        }

        $buttons = BotButton::where('parent_id', $confessionButton->id)->orderBy('order')->get();

        $this->clearButtons();
        $this->menuText(__('telegram.confession_menu'));

        foreach ($buttons as $button) {
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $button->getTranslation('text', app()->getLocale()),
                    callback_data: $button->callback_data . '@handle' . ucfirst(str_replace('_', '', $button->callback_data))
                )
            );
        }

        $this->addButtonRow(
            InlineKeyboardButton::make(
                text: __('telegram.main_menu'),
                callback_data: 'main_menu@backToMain'
            )
        );

        $this->showMenu();
    }

    public function handleViewConfessions(Nutgram $bot): void
    {
        $this->start($bot);
    }

    public function showConfession(Nutgram $bot): void
    {
        // Extract confession ID from callback data
        $callbackData = $bot->callbackQuery()?->data ?? '';
        preg_match('/confession:(\d+)/', $callbackData, $matches);
        $id = $matches[1] ?? null;

        if (!$id) {
            $bot->answerCallbackQuery(text: __('telegram.error_invalid_confession'));
            $this->start($bot);
            return;
        }

        $confession = Confession::find($id);

        if (!$confession) {
            $bot->answerCallbackQuery(text: __('telegram.error_confession_not_found'));
            $this->start($bot);
            return;
        }

        $this->clearButtons();
        $this->menuText(
            $confession->emoji . ' ' . $confession->getTranslation('name', app()->getLocale()) . "\n\n" .
            $confession->getTranslation('description', app()->getLocale())
        );

        foreach (ConfessionActions::cases() as $action) {
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $this->getActionText($action),
                    callback_data: 'action:' . $action->value . ':' . $id . '@handleAction'
                )
            );
        }

        $this->addButtonRow(
            InlineKeyboardButton::make(
                text: __('telegram.button_back'),
                callback_data: 'view_confessions@handleViewConfessions'
            ),
            InlineKeyboardButton::make(
                text: __('telegram.main_menu'),
                callback_data: 'main_menu@backToMain'
            )
        );

        $this->showMenu();
    }

    public function handleAction(Nutgram $bot): void
    {
        // Extract action and ID from callback data
        $callbackData = $bot->callbackQuery()?->data ?? '';
        preg_match('/action:([^:]+):(\d+)/', $callbackData, $matches);
        $action = $matches[1] ?? null;
        $id = $matches[2] ?? null;

        if (!$action || !$id) {
            $bot->answerCallbackQuery(text: __('telegram.error_invalid_action'));
            $this->start($bot);
            return;
        }

        $confession = Confession::find($id);

        if (!$confession) {
            $bot->answerCallbackQuery(text: __('telegram.error_confession_not_found'));
            $this->start($bot);
            return;
        }

        $this->clearButtons();
        $this->menuText(__('telegram.' . $action) . ' - ' . __('telegram.coming_soon'));

        $this->addButtonRow(
            InlineKeyboardButton::make(
                text: __('telegram.button_back'),
                callback_data: 'confession:' . $id . '@showConfession'
            )
        );

        $this->showMenu();
    }

    public function backToMain(Nutgram $bot): void
    {
        MainMenuConversation::begin($bot);
    }

    private function getActionText(ConfessionActions $action): string
    {
        return match ($action) {
            ConfessionActions::LearnAboutConfession => __('telegram.learn_about_confession'),
            ConfessionActions::ConfessionMenuAction => __('telegram.confession_menu_action'),
            ConfessionActions::ConfessionMenuSubAction => __('telegram.confession_menu_sub_action'),
        };
    }
}
