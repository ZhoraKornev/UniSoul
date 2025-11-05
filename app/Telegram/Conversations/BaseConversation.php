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
        ?int $actionId = null
    ): string {
        return (new CallbackData($confession, $action, $actionId, $confessionId, $method))->build();
    }
}
