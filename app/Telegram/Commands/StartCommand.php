<?php

namespace App\Telegram\Commands;

use App\Telegram\Conversations\MainMenuConversation;
use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;

class StartCommand extends Command
{
    protected string $command = 'start';

    protected ?string $description = 'A lovely start command';

    public function handle(Nutgram $bot): void
    {
        MainMenuConversation::begin($bot);
    }
}
