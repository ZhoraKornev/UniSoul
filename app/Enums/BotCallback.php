<?php

namespace App\Enums;

/**
 * Main menu and primary navigation callbacks (MainMenuAction).
 */
enum BotCallback: string
{
    // Primary navigation
    case MainMenu = 'main_menu';
    case BackButton = 'return_back';

    // Confession list and selection flow
    case ConfessionListMenu = 'confession_list_menu';
    case ViewConfession = 'view_confession'; // Used when selecting a specific Confession

}
