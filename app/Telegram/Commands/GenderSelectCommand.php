<?php

namespace App\Telegram\Commands;

use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ChatAction;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class GenderSelectCommand extends Command
{
    protected string $command = 'gender';

    protected ?string $description = 'Prompts user to select gender via inline keyboard.';

    public function handle(Nutgram $bot): void
    {
        // 1. Show the chat is performing an action (optional)
        $bot->sendChatAction(action: ChatAction::TYPING);

        // 2. Define the Inline Keyboard Layout using Nutgram's object syntax
        // The callback_data is what your update handler will receive later.
        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make(
                    text: trans('telegram.gender_male'),
                    callback_data: 'GENDER_MALE'
                ),
                InlineKeyboardButton::make(
                    text: trans('telegram.gender_female'),
                    callback_data: 'GENDER_FEMALE'
                )
            )
            ->addRow(
                InlineKeyboardButton::make(
                    text: trans('telegram.gender_anonymous'),
                    callback_data: 'GENDER_ANONYMOUS'
                )
            );

        // 3. Send the message with the translated text and the inline keyboard
        $bot->sendMessage(
            text: trans('telegram.select_gender'),
            reply_markup: $keyboard
        );

        // NOTE: In a real application, you would now save the user state
        // (e.g., in the database) to expect a callback query for gender selection.
    }
}
