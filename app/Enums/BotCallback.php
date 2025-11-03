<?php

namespace App\Enums;

enum BotCallback: string
{
    public const PREFIX_FOR_FUNCTION = 'handle';
    case MainMenu = 'mainMenu';
    case BackButton = 'backButton';

    case ConfessionListMenu = 'confessionListMenu';
    case ViewConfession = 'viewConfession';
    case LearnAboutConfession = 'learnAboutConfession';

    case ConfessionMenuAction = 'confessionMenuAction';

    case Sorokoust = 'sorokoust';
    case LightACandle = 'lightACandle';
    case SubmitPrayerNote = 'submitPrayerNote';
    case ReadAkathists = 'readAkathists';
    case ReadUnceasingPsalter = 'readUnceasingPsalter';
    case MemorialService = 'memorialService';
    case PriestsList = 'priestsList';
    case Donate = 'donate';

    /**
     * Return localized label.
     *
     * @param string|null $locale Example: 'en', 'uk'
     */
    public function label(?string $locale = null): string
    {
        // Map each enum case to a translation key
        $key = match ($this) {
            self::MainMenu => 'telegram.bot.main_menu',
            self::BackButton => 'telegram.bot.back_button',

            self::ConfessionListMenu => 'telegram.bot.confession_list_menu',
            self::ViewConfession => 'telegram.bot.view_confession',
            self::LearnAboutConfession => 'telegram.bot.learn_about_confession',

            self::ConfessionMenuAction => 'telegram.bot.confession_menu_action',

            self::Sorokoust => 'telegram.confession_actions.sorokoust',
            self::LightACandle => 'telegram.confession_actions.light_a_candle',
            self::SubmitPrayerNote => 'telegram.confession_actions.submit_prayer_note',
            self::ReadAkathists => 'telegram.confession_actions.read_akathists',
            self::ReadUnceasingPsalter => 'telegram.confession_actions.read_unceasing_psalter',
            self::MemorialService => 'telegram.confession_actions.memorial_service',
            self::PriestsList => 'telegram.confession_actions.priests_list',
            self::Donate => 'telegram.confession_actions.donate',
        };

        // __($key, $replace = [], $locale = null)
        return __($key, [], $locale);
    }

    public function camelName(): string
    {
        return lcfirst($this->name);
    }
}
