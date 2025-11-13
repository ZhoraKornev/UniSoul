<?php

namespace App\Enums;

enum BotCallback: string
{
    public const PREFIX_FOR_FUNCTION = 'handle';

    // ─────────────────────────────
    // 📍 Main navigation callbacks
    // ─────────────────────────────
    case MainMenu = 'mainMenu';
    case BackButton = 'backButton';

    // ─────────────────────────────
    // 🙏 Confession flow navigation
    // ─────────────────────────────
    case ConfessionListMenu = 'confessionListMenu';
    case ViewConfession = 'viewConfession';
    case LearnAboutConfession = 'learnAboutConfession';

    // ─────────────────────────────
    // 📂 Confession dynamic submenu root
    // ─────────────────────────────
    case ConfessionMenuAction = 'confessionMenuAction';

    // ─────────────────────────────
    // 🕯️ Confessional actions
    // ─────────────────────────────
    case PriestsList = 'priestsList';
    case Donate = 'donate';
    case ShowBranches = 'showBranches';
    // ─────────────────────────────
    // 🕯️ Branch and emplyers actions
    // ─────────────────────────────

    case Sorokoust = 'sorokoust';
    case LightACandle = 'lightACandle';
    case SubmitPrayerNote = 'submitPrayerNote';
    case ReadAkathists = 'readAkathists';
    case ReadUnceasingPsalter = 'readUnceasingPsalter';
    case MemorialService = 'memorialService';
    case ContactEmployer = 'contactEmployer';
    case EmployerOpenMenu = 'employerOpenMenu';
    case LearnVideosConfession = 'learnVideosConfession';
    case LearnImportantNotationAboutConfession = 'learnImportantNotationAboutConfession';
    case LearnBooksAboutConfession = 'learnBooksAboutConfession';

    /**
     * Get localized label for menu usage.
     */
    public function label(?string $locale = null): string
    {
        $key = match ($this) {
            // 🧭 Main Navigation
            self::MainMenu => 'telegram.bot.main_menu',
            self::BackButton => 'telegram.bot.back_button',

            // 🙏 Confession navigation
            self::ConfessionListMenu => 'telegram.bot.confession_list_menu',
            self::ViewConfession => 'telegram.bot.view_confession',
            self::LearnAboutConfession => 'telegram.bot.learn_about_confession',

            // ➕ Confession submenu root
            self::ConfessionMenuAction => 'telegram.bot.confession_menu_action',

            // 🕯️ Confession actions
            self::Sorokoust => 'telegram.confession_actions.sorokoust',
            self::LightACandle => 'telegram.confession_actions.light_a_candle',
            self::SubmitPrayerNote => 'telegram.confession_actions.submit_prayer_note',
            self::ReadAkathists => 'telegram.confession_actions.read_akathists',
            self::ReadUnceasingPsalter => 'telegram.confession_actions.read_unceasing_psalter',
            self::MemorialService => 'telegram.confession_actions.memorial_service',
            self::PriestsList => 'telegram.confession_actions.priests_list',
            self::Donate => 'telegram.confession_actions.donate',
            self::ShowBranches => 'telegram.confession_actions.show_branches',
            self::ContactEmployer => 'telegram.confession_actions.contact_employer',
            self::EmployerOpenMenu => 'telegram.confession_actions.employer_open_menu',
            self::LearnVideosConfession => 'telegram.confession_actions.learn_videos_confession',
            self::LearnImportantNotationAboutConfession => 'telegram.confession_actions.learn_important_notation_about_confession',
            self::LearnBooksAboutConfession => 'telegram.confession_actions.learn_books_about_confession',
        };

        return __($key, [], $locale);
    }

    /**
     * Convert enum name to camelCase for handler methods.
     */
    public function camelName(): string
    {
        return lcfirst($this->name);
    }
}
