<?php

namespace App\Telegram\Conversations;

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
        $buttons = BotButton::whereNull('parent_id')->orderBy('order')->get();

        $this->clearButtons();
        $this->menuText(trans('telegram.welcome_message'));

        foreach ($buttons as $button) {
            /** @var BotButton $button */
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $button->getTranslation('text', app()->getLocale()),
                    callback_data: $button->callback_data . '@handle' . ucfirst(str_replace('_', '', $button->callback_data))
                )
            );
        }
        /** @var Chat|null $chat */
        $chat = $bot->get(Chat::class);
        $setupConfessionID = $chat->settings()->get(SettingsKeys::CONFESSION->value);
        if ($setupConfessionID !== null){
            /** @var Confession|null $confession */
            $confession = Confession::find($setupConfessionID);
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $confession->emoji . ' ' . $confession->getTranslation('name', app()->getLocale()),
                    callback_data: 'confession:' . $setupConfessionID . '@showConfession'
                )
            );
        }

        $this->showMenu();
    }

    public function handleConfessionlistmenu(Nutgram $bot): void
    {
        ConfessionConversation::begin($bot);
    }

    public function showConfession(Nutgram $bot): void
    {
        $bot->answerCallbackQuery();
        $conversation = ConfessionConversation::begin($bot);
        $conversation->showConfession($bot);
    }
}
