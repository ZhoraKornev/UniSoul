<?php

namespace App\Telegram\Middleware;

use App\Models\Chat;
use Illuminate\Support\Facades\DB;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ChatType;

class CollectChatData
{
    /**
     * @throws \Throwable
     */
    public function __invoke(Nutgram $bot, $next): void
    {
        $user = $bot->user();

        if ($user === null) {
            return;
        }

        // Get the chat type from the current chat context, defaulting to private if null
        $chatType = $bot->chat()?->type->value ?? ChatType::PRIVATE->value;

        // Collect groups/channels data
        if ($chatType !== ChatType::PRIVATE->value) {
            /** @var Chat $chatGroup */
            $chatGroup = Chat::updateOrCreate([
                'chat_id' => $bot->chat()->id,
            ], [
                'type' => $bot->chat()->type->value,
                'first_name' => $bot->chat()->title ?? '',
                'username' => $bot->chat()->username,
            ]);
        }

        // Collect users (private chat or user who sent the message)
        /** @var Chat $chat */
        $chat = DB::transaction(function () use ($chatType, $user) {
            /** @var Chat $chat */
            $chat = Chat::updateOrCreate([
                'chat_id' => $user->id,
            ], [
                'type' => ChatType::PRIVATE->value,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'username' => $user->username,
                'language_code' => $user->language_code,
            ]);

            if (is_null($chat->getAttribute('started_at')) && $chatType === ChatType::PRIVATE->value) {
                $chat->setAttribute('started_at', now());
                $chat->save();
            }

            return $chat;
        });

        $bot->set(Chat::class, $chat);

        $next($bot);
    }
}
