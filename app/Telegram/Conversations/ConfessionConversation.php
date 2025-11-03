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
    /** Start – list confessions */
    public function start(Nutgram $bot): void
    {
        $confessions = Confession::query()->where('active', true)->get();

        $this->clearButtons();
        $this->menuText(__('telegram.select_confession'));

        foreach ($confessions as $confession) {
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $confession->emoji.' '.$confession->getTranslation('name', app()->getLocale()),
                    callback_data: BotCallback::ViewConfession->value.":{$confession->id}@handleViewConfession"
                )
            );
        }

        $this->addButtonRow(
            InlineKeyboardButton::make(
                text: __('telegram.button_back'),
                callback_data: BotCallback::MainMenu->value.'@handleMainMenu'
            )
        );

        $this->showMenu();
    }

    /** Show confession + actions */
    public function handleViewConfession(Nutgram $bot): void
    {
        $data = $bot->callbackQuery()?->data ?? '';
        preg_match('/viewConfession:(\d+)/', strtolower($data), $m);
        $id = $m[1] ?? null;

        $confession = Confession::find($id);

        if (!$confession) {
            $bot->answerCallbackQuery(text: __('telegram.error_confession_not_found'));
            $this->start($bot);
        }

        $this->clearButtons();
        $this->menuText(
            $confession->emoji.' '.$confession->getTranslation('name', app()->getLocale())."\n\n".
            $confession->getTranslation('description', app()->getLocale())
        );

        // Fetch root-level confession buttons: entity_type + entity_id + parent null
        $buttons = BotButton::query()
            ->where('entity_type', Confession::class)
            ->where('entity_id', $confession->id)
            ->whereNull('parent_id')
            ->where('active', true)
            ->orderBy('order')
            ->get();

        foreach ($buttons as $button) {
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $button->getTranslation('text', app()->getLocale()),
                    callback_data: $button->callback_data.'@handle'.ucfirst($button->callback_data)
                )
            );
        }

        $this->addButtonRow(
            InlineKeyboardButton::make(
                text: __('telegram.main_menu'),
                callback_data: BotCallback::MainMenu->value.'@handleMainMenu'
            )
        );

        $this->showMenu();
    }

    /** Handles confession action → shows submenu */
    public function __call(string $name, array $arguments)
    {
        if (!str_starts_with($name, 'handle')) {
            throw new \RuntimeException("Unknown callback: ".$name);
        }

        $bot = $arguments[0] ?? null;
        $raw = $bot->callbackQuery()?->data ?? '';

        preg_match('/([^@]+)@handle([^:]+)(?::(\d+))?/i', $raw, $m);

        $callback = $m[1] ?? null;
        $method   = $m[2] ?? null;
        $confId   = $m[3] ?? null;

        $confession = $confId ? Confession::find($confId) : null;

        // submenu buttons for this callback
        $parent = BotButton::where('callback_data', $callback)
            ->where('entity_type', Confession::class)
            ->where('entity_id', optional($confession)->id)
            ->first();

        if (!$parent) {
            $bot->answerCallbackQuery(text: __('telegram.error_action'));
            $this->start($bot);
        }

        $children = BotButton::where('parent_id', $parent->id)->orderBy('order')->get();

        $this->clearButtons();
        $this->menuText($parent->getTranslation('text', app()->getLocale()));

        foreach ($children as $btn) {
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $btn->getTranslation('text', app()->getLocale()),
                    callback_data: $btn->callback_data.'@handle'.ucfirst($btn->callback_data).':'.$confession->id
                )
            );
        }

        $this->addButtonRow(
            InlineKeyboardButton::make(
                text: __('telegram.button_back'),
                callback_data: BotCallback::ViewConfession->value.":{$confession->id}@handleViewConfession"
            )
        );

        $this->showMenu();
    }

    /** Back to main menu */
    public function handleMainMenu(Nutgram $bot): void
    {
        MainMenuConversation::begin($bot);
    }
}
