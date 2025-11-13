<?php

namespace App\Telegram\Conversations;

use App\Telegram\DTOs\CallbackData;
use SergiX44\Nutgram\Conversations\InlineMenu;

abstract class BaseConversation extends InlineMenu
{
    protected function parseCallbackData(string $data): CallbackData
    {
        return CallbackData::parse($data);
    }

    protected function buildCallbackData(
        string $confession,
        ?int $confessionId,
        string $action,
        string $method,
        ?int $actionId = null,
        ?int $page = null,
    ): string {
        return (new CallbackData(
            confession: $confession,
            action: $action,
            actionId: $actionId,
            confessionId: $confessionId,
            method: $method,
            page: $page,
        ))->build();
    }
}
