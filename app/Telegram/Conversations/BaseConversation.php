<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\InlineMenu;
use SergiX44\Nutgram\Nutgram;

abstract class BaseConversation extends InlineMenu
{
    protected function parseCallbackData(string $data): array
    {
        if (str_contains($data, ':')) {
            preg_match('/([^:]+):(\d+)/', $data, $matches);
            return [
                'action' => $matches[1] ?? null,
                'id' => $matches[2] ?? null,
                'method' => null
            ];
        }

        return ['action' => null, 'id' => null, 'method' => null];
    }

    protected function buildCallbackData(string $action, string $method, ?int $id = null): string
    {
        if ($id) {
            return "{$action}:{$id}@{$method}";
        }

        return "{$action}@{$method}";
    }
}
