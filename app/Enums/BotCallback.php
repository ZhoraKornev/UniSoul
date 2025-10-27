<?php

namespace App\Enums;

enum BotCallback: string
{
    case ConfessionMenu = 'confession_menu';
    case ViewConfessions = 'view_confessions';
    case BackToStart = 'back_to_start';
    case SelectConfession = 'select_confession';

    // Text-based actions (for reply keyboards)
    case BackButton = 'text:back';
    case ConfessionMenuButton = 'text:confession_menu';
    case ViewConfessionsButton = 'text:view_confessions';

    /**
     * Check if this is a text-based callback
     */
    public function isTextBased(): bool
    {
        return str_starts_with($this->value, 'text:');
    }

    /**
     * Get the display text for text-based callbacks
     */
    public function getDisplayText(): string
    {
        return match($this) {
            self::BackButton => __('telegram.button_back'),
            self::ConfessionMenuButton => '🙏 ' . __('telegram.confession_menu'),
            self::ViewConfessionsButton => '📖 ' . __('telegram.view_confessions'),
            default => $this->value,
        };
    }
}
