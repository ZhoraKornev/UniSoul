<?php

namespace App\Telegram\Conversations;

use App\Enums\BotCallback;
use App\Enums\SettingsKeys;
use App\Models\BotButton;
use App\Models\Chat;
use App\Models\Confession;
use SergiX44\Nutgram\Conversations\InlineMenu;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

class MainMenuConversation extends InlineMenu
{
    public function start(Nutgram $bot): void
    {
        $locale = app()->getLocale();
        $buttons = BotButton::whereNull('parent_id')->orderBy('order')->get();

        $this->clearButtons();
        $this->menuText(trans('telegram.welcome_message'));

        foreach ($buttons as $button) {
            /** @var \App\Models\BotButton $button */
            $label = $button->getTranslation('text', $locale);

            $callbackKey = $button->callback_data->value;
            $callbackHandlerMethod = BotCallback::PREFIX_FOR_FUNCTION . $button->callbackEnum()->name;

            $callback = "{$callbackKey}@{$callbackHandlerMethod}";

            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $label,
                    callback_data: $callback
                )
            );
        }

        /** @var Chat|null $chat */
        $chat = $bot->get(Chat::class);
        $confessionId = $chat?->settings()?->get(SettingsKeys::CONFESSION->value);

        if ($confessionId) {
            $confession = Confession::find($confessionId);

            if ($confession) {
                $this->addButtonRow(
                    InlineKeyboardButton::make(
                        text: $confession->emoji . ' ' . $confession->getTranslation('name', $locale),
                        callback_data: "view_confession:{$confession->id}@viewConfession"
                    )
                );
            }
        }

        $this->showMenu();
    }

    public function handleConfessionListMenu(Nutgram $bot): void
    {
        $bot->answerCallbackQuery();
        ConfessionConversation::begin($bot);
    }

    public function viewConfession(Nutgram $bot): void
    {
        $bot->answerCallbackQuery();
        $conversation = ConfessionConversation::begin($bot);
        $conversation->handleViewConfession($bot);
    }

    public function handleUnknown(Nutgram $bot): void
    {
        $bot->answerCallbackQuery('Unknown action ❓');
    }
}
