<?php

namespace App\Telegram\Middleware;

use App\Models\Chat;
use Illuminate\Support\Facades\App;
use SergiX44\Nutgram\Nutgram;

class SetUserLocaleMiddleware
{
    public function __invoke(Nutgram $bot, $next): void
    {
        /** @var Chat $chat */
        $chat = $bot->get(Chat::class);

        $userLang = $chat->settings()->get('language') ?? config('app.locale');
        $supportedLangs = config('app.supported_languages', ['en', 'uk']);

        $locale = in_array($userLang, $supportedLangs) ? $userLang : 'en';
        App::setLocale($locale);

        $next($bot);
    }
}
