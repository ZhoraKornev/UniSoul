<?php

namespace App\Telegram\Conversations;

use App\Enums\Gender;
use App\Enums\SettingsKeys;
use App\Models\Chat;
use App\Models\Confession;
use Illuminate\Support\Facades\App;
use SergiX44\Nutgram\Conversations\InlineMenu;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

class SettingsConversation extends InlineMenu
{
    public function start(Nutgram $bot): void
    {
        /** @var Chat|null $chat */
        $chat = $bot->get(Chat::class);

        // Ensure the chat record exists before proceeding
        if (! $chat) {
            $bot->sendMessage(trans('telegram.chat_not_found'));
            $this->end();

            return;
        }

        $this
            ->clearButtons()
            ->menuText($this->getSettingsMainMessage($chat), [
                'parse_mode' => ParseMode::HTML,
                'disable_web_page_preview' => true,
            ])
            ->addButtonRow(
                InlineKeyboardButton::make(
                    text: trans('settings.language.title'),
                    callback_data: 'settings:languages@handleLanguages'
                ),
                InlineKeyboardButton::make(
                    text: trans('settings.gender.title'),
                    callback_data: 'settings:gender@handleGender'
                )
            )
            ->addButtonRow(
                InlineKeyboardButton::make(
                    text: trans('settings.confession.title'),
                    callback_data: 'settings:confession@handleConfession'
                )
            )
            ->addButtonRow(
                InlineKeyboardButton::make(
                    text: trans('telegram.close'),
                    callback_data: 'settings:cancel@end'
                )
            )
            ->showMenu();
    }

    protected function handleLanguages(Nutgram $bot): void
    {
        /** @var Chat|null $chat */
        $chat = Chat::find($bot->userId());

        if (! $chat) {
            $bot->sendMessage(trans('telegram.chat_not_found'));
            $this->end();

            return;
        }

        $this
            ->clearButtons()
            ->menuText($this->getLanguageSettingsMessage($chat), [
                'parse_mode' => ParseMode::HTML,
                'disable_web_page_preview' => true,
            ]);

        collect($this->getAvailableLanguages())
            ->map(fn ($item, $key) => InlineKeyboardButton::make($item, callback_data: "language:$key@setLanguage"))
            ->chunk(2)
            ->each(fn ($row) => $this->addButtonRow(...$row->values()));

        $this->addButtonRow(InlineKeyboardButton::make(trans('telegram.button_back'), callback_data: 'languages:back@start'));

        $this->showMenu();
    }

    protected function setLanguage(Nutgram $bot): void
    {
        [, $language] = explode(':', $bot->callbackQuery()->data);

        /** @var Chat|null $chat */
        $chat = Chat::find($bot->userId());

        if (! $chat) {
            $bot->sendMessage(trans('telegram.chat_not_found'));
            $this->end();

            return;
        }

        $currentLanguage = $chat->settings()->get(SettingsKeys::LANGUAGE->value);

        // If same language selected, go back to main menu
        if ($currentLanguage === $language) {
            $bot->answerCallbackQuery();
            $this->start($bot);

            return;
        }

        // Use the settings relationship, now safely typed as Chat
        $chat->settings()->set(SettingsKeys::LANGUAGE->value, $language);

        // Fix: Use ternary operator to handle potential empty string/null for language
        App::setLocale($language ?: config('app.locale'));

        $bot->answerCallbackQuery();
        $this->start($bot);
    }

    protected function handleGender(Nutgram $bot): void
    {
        /** @var Chat|null $chat */
        $chat = Chat::find($bot->userId());

        if (! $chat) {
            $bot->sendMessage(trans('telegram.chat_not_found'));
            $this->end();

            return;
        }

        $this
            ->clearButtons()
            ->menuText($this->getGenderSettingsMessage($chat), [
                'parse_mode' => ParseMode::HTML,
                'disable_web_page_preview' => true,
            ]);

        foreach (Gender::cases() as $gender) {
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $gender->emoji().' '.$gender->label(),
                    callback_data: "gender:{$gender->value}@setGender"
                )
            );
        }

        $this->addButtonRow(
            InlineKeyboardButton::make(
                trans('telegram.button_back'),
                callback_data: 'gender:back@start'
            )
        );

        $this->showMenu();
    }

    protected function setGender(Nutgram $bot): void
    {
        [, $genderValue] = explode(':', $bot->callbackQuery()->data);

        /** @var Chat|null $chat */
        $chat = Chat::find($bot->userId());

        if (! $chat) {
            $bot->sendMessage(trans('telegram.chat_not_found'));
            $this->end();

            return;
        }

        $currentGender = $chat->settings()->get(SettingsKeys::GENDER->value);

        // If same gender selected, go back to main menu
        if ($currentGender === $genderValue) {
            $bot->answerCallbackQuery();
            $this->start($bot);

            return;
        }

        // Use the settings relationship, now safely typed as Chat
        $chat->settings()->set(SettingsKeys::GENDER->value, $genderValue);

        $bot->answerCallbackQuery();
        $this->start($bot);
    }

    protected function handleConfession(Nutgram $bot): void
    {
        /** @var Chat|null $chat */
        $chat = Chat::find($bot->userId());

        if (! $chat) {
            $bot->sendMessage(trans('telegram.chat_not_found'));
            $this->end();

            return;
        }

        $this
            ->clearButtons()
            ->menuText($this->getConfessionSettingsMessage($chat), [
                'parse_mode' => ParseMode::HTML,
                'disable_web_page_preview' => true,
            ]);

        // Explicitly type the collection for better static analysis
        /** @var \Illuminate\Database\Eloquent\Collection<Confession> $confessions */
        $confessions = Confession::query()
            ->where('active', true)
            ->get();

        /** @var Confession $confession */
        foreach ($confessions as $confession) {
            // Properties and methods are now correctly recognized on Confession model
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $confession->emoji.' '.$confession->getTranslation('name', app()->getLocale()),
                    callback_data: "settings:{$confession->id}@setConfession"
                )
            );
        }

        $this->addButtonRow(
            InlineKeyboardButton::make(
                trans('telegram.button_back'),
                callback_data: 'confession:back@start'
            )
        );

        $this->showMenu();
    }

    protected function setConfession(Nutgram $bot): void
    {
        [, $confessionId] = explode(':', $bot->callbackQuery()->data);

        /** @var Chat|null $chat */
        $chat = Chat::find($bot->userId());

        if (! $chat) {
            $bot->sendMessage(trans('telegram.chat_not_found'));
            $this->end();

            return;
        }

        $currentConfession = $chat->settings()->get(SettingsKeys::CONFESSION->value);

        // If same confession selected, go back to main menu
        if ($currentConfession == $confessionId) {
            $bot->answerCallbackQuery();
            $this->start($bot);

            return;
        }

        // Use the settings relationship, now safely typed as Chat
        $chat->settings()->set(SettingsKeys::CONFESSION->value, (int) $confessionId);

        $bot->answerCallbackQuery();
        $this->start($bot);
    }

    /**
     * Get the main settings message.
     */
    protected function getSettingsMainMessage(Chat $chat): string
    {
        $currentLanguage = $this->getLanguageName($chat->settings()->get(SettingsKeys::LANGUAGE->value));
        $currentGender = $this->getGenderDisplay($chat->settings()->get(SettingsKeys::GENDER->value));
        // Correcting property to 'confession' based on setConfession logic
        $currentConfession = $this->getConfessionDisplay($chat->settings()->get(SettingsKeys::CONFESSION->value));

        // Localized message assembly using interpolation for the 'main' key
        return trans('settings.main', [
            SettingsKeys::LANGUAGE->value => $currentLanguage,
            SettingsKeys::GENDER->value => $currentGender,
            SettingsKeys::CONFESSION->value => $currentConfession,
        ]);
    }

    /**
     * Get the language settings message.
     */
    protected function getLanguageSettingsMessage(Chat $chat): string
    {
        $currentLanguage = $this->getLanguageName($chat->settings()->get(SettingsKeys::LANGUAGE->value));

        // Localized message assembly using interpolation for current language status
        return trans('settings.language.description', [SettingsKeys::LANGUAGE->value => $currentLanguage]);
    }

    /**
     * Get the gender settings message.
     */
    protected function getGenderSettingsMessage(Chat $chat): string
    {
        $currentGender = $this->getGenderDisplay($chat->settings()->get(SettingsKeys::GENDER->value));

        // Localized message assembly using interpolation for current gender status
        return trans('settings.gender.description', [SettingsKeys::GENDER->value => $currentGender]);
    }

    /**
     * Get the confession settings message.
     */
    protected function getConfessionSettingsMessage(Chat $chat): string
    {
        // Correcting property to 'confession' based on setConfession logic
        $currentConfession = $this->getConfessionDisplay($chat->settings()->get(SettingsKeys::CONFESSION->value));

        // Localized message assembly using interpolation for current confession status
        return trans('settings.confession.description', [SettingsKeys::CONFESSION->value => $currentConfession]);
    }

    /**
     * Get available languages.
     */
    protected function getAvailableLanguages(): array
    {
        $supportedLanguages = config('app.supported_languages', ['en', 'uk']);

        return collect($supportedLanguages)
            ->mapWithKeys(fn ($code) => [$code => $this->getLanguageName($code)])
            ->toArray();
    }

    /**
     * Get language display name.
     */
    protected function getLanguageName(?string $code): string
    {
        if (! $code) {
            return trans('settings.not_set');
        }

        $languages = [
            'en' => '🇬🇧 English',
            'uk' => '🇺🇦 Українська',
            'ru' => '🇷🇺 Русский',
            'de' => '🇩🇪 Deutsch',
            'ro' => '🇷🇴 Română',
            'ka' => '🇬🇪 ქართული',
        ];

        return $languages[$code] ?? strtoupper($code);
    }

    /**
     * Get gender display name.
     */
    protected function getGenderDisplay(?string $genderValue): string
    {
        if (! $genderValue) {
            return trans('settings.not_set');
        }

        $gender = Gender::tryFrom($genderValue);

        return $gender
            ? $gender->emoji().' '.$gender->label()
            : trans('settings.not_set');
    }

    /**
     * Get confession display name.
     */
    protected function getConfessionDisplay(?int $confessionId): string
    {
        if (! $confessionId) {
            return trans('settings.not_set');
        }

        /** @var Confession|null $confession */
        $confession = Confession::find($confessionId);

        // Properties and methods are now correctly recognized on Confession model
        return $confession
            ? $confession->emoji.' '.$confession->getTranslation('name', app()->getLocale())
            : trans('settings.not_set');
    }
}
