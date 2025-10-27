<?php

namespace App\Telegram\Middleware;

use Illuminate\Support\Facades\App;
use SergiX44\Nutgram\Nutgram;

class SetUserLocaleMiddleware
{
    public function __invoke(Nutgram $bot, $next): void
    {
        $userLang = substr($bot->user()?->language_code ?? 'uk', 0, 2);
        $supportedLangs = config('app.supported_languages', ['en', 'uk']);

        $locale = in_array($userLang, $supportedLangs) ? $userLang : 'en';
        App::setLocale($locale);

        $next($bot);
    }
}
