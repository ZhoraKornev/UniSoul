<?php

namespace App\Telegram\Conversations;

use App\Models\BotButton;
use App\Models\Confession;
use App\Enums\ConfessionActions;
use SergiX44\Nutgram\Conversations\InlineMenu;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use App\Telegram\Conversations\MainMenuConversation;

// Added: Assuming this is where backToMain points

class ConfessionConversation extends InlineMenu
{
    public function start(Nutgram $bot): void
    {
        // Explicitly type the collection elements for static analysis
        /** @var \Illuminate\Database\Eloquent\Collection<Confession> $confessions */
        $confessions = Confession::where('active', true)->get();

        $this->clearButtons();
        $this->menuText(__('telegram.select_confession'));

        /** @var Confession $confession */
        foreach ($confessions as $confession) {
            // Properties and methods are now recognized on Confession (L24, L25 errors resolved)
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
        /** @var BotButton|null $confessionButton */
        $confessionButton = BotButton::where('callback_data', 'confession_menu')->first();

        if (!$confessionButton) {
            $this->start($bot);
            return;
        }

        /** @var \Illuminate\Database\Eloquent\Collection<BotButton> $buttons */
        $buttons = BotButton::where('parent_id', $confessionButton->id)->orderBy('order')->get();

        $this->clearButtons();
        $this->menuText(__('telegram.confession_menu'));

        /** @var BotButton $button */
        foreach ($buttons as $button) {
            $this->addButtonRow(
                InlineKeyboardButton::make(
                // getTranslation is now recognized on BotButton (No explicit error, but good practice)
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
        // Refactored nullsafe access (L85 error resolved)
        $callbackQuery = $bot->callbackQuery();
        $callbackData = $callbackQuery ? $callbackQuery->data : '';
        preg_match('/confession:(\d+)/', $callbackData, $matches);
        $id = $matches[1] ?? null;

        if (!$id) {
            $bot->answerCallbackQuery(text: __('telegram.error_invalid_confession'));
            $this->start($bot);
            return;
        }

        /** @var Confession|null $confession */
        $confession = Confession::find($id);

        if (!$confession) {
            $bot->answerCallbackQuery(text: __('telegram.error_confession_not_found'));
            $this->start($bot);
            return;
        }

        $this->clearButtons();
        $this->menuText(
        // Properties and methods are now recognized on Confession (L61 error resolved)
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
        // Refactored nullsafe access (L135 error resolved)
        $callbackQuery = $bot->callbackQuery();
        $callbackData = $callbackQuery ? $callbackQuery->data : '';
        preg_match('/action:([^:]+):(\d+)/', $callbackData, $matches);
        $action = $matches[1] ?? null;
        $id = $matches[2] ?? null;

        if (!$action || !$id) {
            $bot->answerCallbackQuery(text: __('telegram.error_invalid_action'));
            $this->start($bot);
            return;
        }

        /** @var Confession|null $confession */
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
        // Ensure MainMenuConversation is correctly imported and available
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
