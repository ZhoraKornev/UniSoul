<?php

namespace App\Contracts;

use SergiX44\Nutgram\Nutgram;

interface ActionHandler
{
    public function isSupport(string $actionCallbackName): bool;

    public function handle(Nutgram $bot): void;

}
