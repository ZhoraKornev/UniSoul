<?php

namespace App\Telegram\Conversations;

use App\Models\BotButton;
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
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $button->getTranslation('text', app()->getLocale()),
                    callback_data: $button->callback_data . '@handle' . ucfirst(str_replace('_', '', $button->callback_data))
                )
            );
        }

        $this->showMenu();
    }

    public function handleConfessionlistmenu(Nutgram $bot): void
    {
        ConfessionConversation::begin($bot);
    }
}
