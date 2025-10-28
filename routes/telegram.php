<?php

use App\Services\HandlerProvider;
use App\Telegram\Commands\StartCommand;
use App\Telegram\Middleware\SetUserLocaleMiddleware;
use SergiX44\Nutgram\Nutgram;

/** @var Nutgram $bot */

$bot->middleware(SetUserLocaleMiddleware::class);

$bot->registerCommand(StartCommand::class);

// Handle callback queries (inline keyboard buttons)
$bot->onCallbackQueryData('.*', function (Nutgram $bot) {
    $callbackData = $bot->callbackQuery()->data;

//    try {
        $provider = app(HandlerProvider::class);
        $handler = $provider->provide($callbackData);
        $handler->handle($bot);

        // Answer callback query to remove loading state
        $bot->answerCallbackQuery();
//    } catch (\RuntimeException $e) {
//        logger()->warning('Callback handler not found', [
//            'callback_data' => $callbackData,
//            'user_id' => $bot->userId(),
//        ]);
//
//        $bot->answerCallbackQuery(
//            text: __('telegram.error_unknown_action'),
//            show_alert: false,
//        );
//    }
});

// Handle text messages (reply keyboard buttons) - only if not a command
$bot->onText('.*', function (Nutgram $bot) {
    $text = $bot->message()->text;

    // Skip if it's a command (starts with /)
    if (str_starts_with($text, '/')) {
        return;
    }

//    try {
        $provider = app(HandlerProvider::class);
        $handler = $provider->provide($text);
        $handler->handle($bot);
//    } catch (\RuntimeException $e) {
//        logger()->warning('Text handler not found', [
//            'text' => $text,
//            'user_id' => $bot->userId(),
//        ]);
//
//        $bot->sendMessage(
//            text: __('telegram.unknown_command')
//        );
//    }
});

// Fallback for unhandled updates
$bot->fallback(function (Nutgram $bot) {
    $bot->sendMessage(
        text: __('telegram.command_not_recognized')
    );
});

