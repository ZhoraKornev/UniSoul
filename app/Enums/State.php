<?php

namespace App\Enums;

enum State: int
{
    // --- 1x: Active/Ready States ---
    // User is ready to interact or in a standard loop.
    case Ready = 10;
    case ActiveConversation = 11;

    // --- 2x: Non-Active/Restricted States ---
    // User cannot interact or has voluntarily exited.
    case Banned = 20;
    case Stopped = 21; // User stopped the bot (/stop or blocked)
    case AdminSuspended = 22; // Suspended by an admin action

    // --- 3x: Temporary/Flow States (Awaiting input) ---
    // User is in the middle of a specific flow (e.g., registration, setting).
    case AwaitingName = 30;
    case AwaitingEmail = 31;
    case AwaitingInput = 32;
    case AwaitingConfirmation = 33;

    /**
     * Check if the user is in any active state (1x group).
     */
    public function isActive(): bool
    {
        // Changed to use the match expression for clarity and to satisfy PHPStan's non-trivial comparison check.
        // It specifically targets cases 10 and 11, which are the only 'active' states.
        return match ($this) {
            self::Ready, self::ActiveConversation => true,
            default => false,
        };
    }
}
