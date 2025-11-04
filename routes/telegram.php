<?php

use App\Enums\BotCallback;
use App\Telegram\Commands\HelpCommand;
use App\Telegram\Commands\SettingsCommand;
use App\Telegram\Commands\StartCommand;
use App\Telegram\Conversations\MainMenuConversation;
use App\Telegram\Middleware\CheckMaintenance;
use App\Telegram\Middleware\CollectChatData;
use App\Telegram\Middleware\SetUserLocaleMiddleware;
use SergiX44\Nutgram\Nutgram;

/** @var Nutgram $bot */

$bot->middleware(CollectChatData::class);
$bot->middleware(CheckMaintenance::class);
$bot->middleware(SetUserLocaleMiddleware::class);

$bot->registerCommand(StartCommand::class);
$bot->registerCommand(SettingsCommand::class);
$bot->registerCommand(HelpCommand::class);

$bot->onMessage(function (Nutgram $bot) {
    if (!str_starts_with($bot->message()->text ?? '', '/')) {
        MainMenuConversation::begin($bot);
    }
});

// Main menu navigation
$bot->onCallbackQueryData(BotCallback::MainMenu->value . '@backToMain', function (Nutgram $bot) {
    MainMenuConversation::begin($bot);
});

// No need for separate routes - InlineMenu handles callback patterns internally
// The conversation classes will handle their own callback data routing

// Fallback for unhandled updates
$bot->fallback(function (Nutgram $bot) {
    $bot->sendMessage(
        text: __('telegram.command_not_recognized')
    );
});
