<?php

namespace App\Telegram\Conversations;

use App\Enums\BotCallback;
use App\Models\BotButton;
use App\Models\Confession;
use SergiX44\Nutgram\Conversations\InlineMenu;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

class ConfessionConversation extends InlineMenu
{
    public function start(Nutgram $bot): void
    {
        $confessions = Confession::query()->where('active', true)->get();

        $this->clearButtons();
        $this->menuText(__('telegram.select_confession'));

        foreach ($confessions as $confession) {
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $confession->emoji . ' ' . $confession->getTranslation('name', app()->getLocale()),
                    callback_data: MainMenuConversation::CONFESSION_PREFIX . ":{$confession->id}@handleViewConfession"
                )
            );
        }

        $this->addButtonRow(
            InlineKeyboardButton::make(
                text: __('telegram.button_back'),
                callback_data: BotCallback::MainMenu->value . '@handleMainMenu'
            )
        );

        if ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery();
        }

        $this->showMenu();
    }

    public function handleViewConfession(Nutgram $bot): void
    {
        $data = $bot->callbackQuery()?->data ?? '';
        preg_match('/' . MainMenuConversation::CONFESSION_PREFIX . ':(\d+)/', strtolower($data), $m);
        $id = $m[1] ?? null;

        $confession = Confession::find($id);

        if (!$confession) {
            $bot->answerCallbackQuery(text: __('telegram.error_confession_not_found'), show_alert: true);
            MainMenuConversation::begin($bot);

            return;
        }

        $this->clearButtons();
        $this->menuText(
            $confession->emoji . ' ' . $confession->getTranslation('name', app()->getLocale()) . "\n\n" .
            $confession->getTranslation('description', app()->getLocale())
        );
        $confessionRootButtonId = BotButton::whereCallbackData(BotCallback::ConfessionListMenu->value)->select('id')->first()?->id;
        if (!$confessionRootButtonId) {
            return;
        }

        $buttons = BotButton::query()
            ->where('entity_type', Confession::class)
            ->where('entity_id', $confession->id)
            ->where('parent_id', $confessionRootButtonId)
            ->where('active', true)
            ->orderBy('order')
            ->get();

        foreach ($buttons as $button) {
            /** @var BotButton $button */
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $button->getTranslation('text', app()->getLocale()),
                    callback_data: $button->callback_data->name . '@' . BotCallback::PREFIX_FOR_FUNCTION . $button->callback_data->name
                )
            );
        }

        $this->addButtonRow(
            InlineKeyboardButton::make(
                text: __('telegram.main_menu'),
                callback_data: BotCallback::MainMenu->value . '@handleMainMenu'
            )
        );

        if ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery();
        }

        $this->showMenu();
    }

    public function handleLearnAboutConfession(Nutgram $bot): void
    {

        $bot->answerCallbackQuery(text: __('telegram.error_confession_not_found'), show_alert: true);
        MainMenuConversation::begin($bot);


        $this->showMenu();
    }

    public function handleConfessionMenuAction(Nutgram $bot): void
    {

        $bot->answerCallbackQuery(text: __('telegram.error_confession_not_found'), show_alert: true);
        MainMenuConversation::begin($bot);


        $this->showMenu();
    }

    public function __call(string $name, array $arguments)
    {
        if (!str_starts_with($name, 'handle')) {
            throw new \RuntimeException("Unknown callback: " . $name);
        }

        $bot = $arguments[0] ?? null;
        $raw = $bot->callbackQuery()?->data ?? '';

        preg_match('/([^@]+)@handle([^:]+)(?::(\d+))?/i', $raw, $m);

        $callback = $m[1] ?? null;
        $method = $m[2] ?? null;
        $confId = $m[3] ?? null;

        $confession = $confId ? Confession::find($confId) : null;

        $parent = BotButton::where('callback_data', $callback)
            ->where('entity_type', Confession::class)
            ->where('entity_id', optional($confession)->id)
            ->first();

        if (!$parent) {
            $bot->answerCallbackQuery(text: __('telegram.error_action'), showAlert: true);
            MainMenuConversation::begin($bot); // Safer redirection
            return;
        }

        $children = BotButton::where('parent_id', $parent->id)->orderBy('order')->get();

        $this->clearButtons();
        $this->menuText($parent->getTranslation('text', app()->getLocale()));

        foreach ($children as $btn) {
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $btn->getTranslation('text', app()->getLocale()),
                    callback_data: $btn->callback_data . '@handle' . ucfirst($btn->callback_data) . ':' . $confession->id
                )
            );
        }

        $this->addButtonRow(
            InlineKeyboardButton::make(
                text: __('telegram.button_back'),
                callback_data: BotCallback::ViewConfession->value . ":{$confession->id}@handleViewConfession"
            )
        );

        if ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery();
        }

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
    public function handleMainMenu(Nutgram $bot): void
    {
        MainMenuConversation::begin($bot);
    }
}
