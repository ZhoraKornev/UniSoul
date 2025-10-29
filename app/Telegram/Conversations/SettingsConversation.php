<?php

namespace App\Telegram\Conversations;

use App\Enums\Gender;
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
        $chat = Chat::find($bot->userId());

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
                    text: '❌ ' . trans('common.close'),
                    callback_data: 'settings:cancel@end'
                )
            )
            ->showMenu();
    }

    protected function handleLanguages(Nutgram $bot): void
    {
        $chat = Chat::find($bot->userId());

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

        $this->addButtonRow(InlineKeyboardButton::make(trans('settings.back'), callback_data: 'languages:back@start'));

        $this->showMenu();
    }

    protected function setLanguage(Nutgram $bot): void
    {
        [, $language] = explode(':', $bot->callbackQuery()->data);

        $chat = Chat::find($bot->userId());
        $chat->settings()->set('language', $language);

        App::setLocale($language ?? config('app.locale'));

        $this->handleLanguages($bot);
    }

    protected function handleGender(Nutgram $bot): void
    {
        $chat = Chat::find($bot->userId());

        $this
            ->clearButtons()
            ->menuText($this->getGenderSettingsMessage($chat), [
                'parse_mode' => ParseMode::HTML,
                'disable_web_page_preview' => true,
            ]);

        foreach (Gender::cases() as $gender) {
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $gender->emoji() . ' ' . $gender->label(),
                    callback_data: "gender:{$gender->value}@setGender"
                )
            );
        }

        $this->addButtonRow(
            InlineKeyboardButton::make(
                trans('settings.back'),
                callback_data: 'gender:back@start'
            )
        );

        $this->showMenu();
    }

    protected function setGender(Nutgram $bot): void
    {
        [, $genderValue] = explode(':', $bot->callbackQuery()->data);

        $chat = Chat::find($bot->userId());
        $chat->settings()->set('gender', $genderValue);

        $this->handleGender($bot);
    }

    protected function handleConfession(Nutgram $bot): void
    {
        $chat = Chat::find($bot->userId());

        $this
            ->clearButtons()
            ->menuText($this->getConfessionSettingsMessage($chat), [
                'parse_mode' => ParseMode::HTML,
                'disable_web_page_preview' => true,
            ]);

        $confessions = Confession::query()
            ->where('active', true)
            ->get();

        foreach ($confessions as $confession) {
            $this->addButtonRow(
                InlineKeyboardButton::make(
                    text: $confession->emoji . ' ' . $confession->getTranslation('name', app()->getLocale()),
                    callback_data: "settings:{$confession->id}@setConfession"
                )
            );
        }

        $this->addButtonRow(
            InlineKeyboardButton::make(
                trans('settings.back'),
                callback_data: 'confession:back@start'
            )
        );

        $this->showMenu();
    }

    protected function setConfession(Nutgram $bot): void
    {
        [, $confessionId] = explode(':', $bot->callbackQuery()->data);

        $chat = Chat::find($bot->userId());
        $chat->settings()->set('confession', (int)$confessionId);

        $this->handleConfession($bot);
    }


    /**
     * Get the main settings message.
     */
    protected function getSettingsMainMessage(Chat $chat): string
    {
        $currentLanguage = $this->getLanguageName($chat->settings()->get('language'));
        $currentGender = $this->getGenderDisplay($chat->settings()->get('gender'));
        $currentConfession = $this->getConfessionDisplay($chat->settings()->get('confession_id'));

        return "Current settings:\n\n💬 Language: " . $currentLanguage . "\n👤 Gender: " . $currentGender . "\n🙏 Confession: " . $currentConfession;
    }

    /**
     * Get the language settings message.
     */
    protected function getLanguageSettingsMessage(Chat $chat): string
    {
        $currentLanguage = $this->getLanguageName($chat->settings()->get('language'));

        return "Language settings\n\nCurrent language: " . $currentLanguage;
    }

    /**
     * Get the gender settings message.
     */
    protected function getGenderSettingsMessage(Chat $chat): string
    {
        $currentGender = $this->getGenderDisplay($chat->settings()->get('gender'));

        return "Gender settings\n\nCurrent gender: " . $currentGender;
    }

    /**
     * Get the confession settings message.
     */
    protected function getConfessionSettingsMessage(Chat $chat): string
    {
        $currentConfession = $this->getConfessionDisplay($chat->settings()->get('confession_id'));

        return "Confession settings\n\nCurrent confession: " . $currentConfession;
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
        if (!$code) return 'Not set';

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
        if (!$genderValue) {
            return trans('settings.gender.not_set');
        }

        $gender = Gender::tryFrom($genderValue);

        return $gender
            ? $gender->emoji() . ' ' . $gender->label()
            : trans('settings.gender.not_set');
    }

    /**
     * Get confession display name.
     */
    protected function getConfessionDisplay(?int $confessionId): string
    {
        if (!$confessionId) {
            return trans('settings.confession.not_set');
        }

        $confession = Confession::find($confessionId);

        return $confession
            ? $confession->emoji . ' ' . $confession->getTranslation('name', app()->getLocale())
            : trans('settings.confession.not_set');
    }
}
