<?php

namespace App\Enums;

/**
 * Top-level actions available after a specific confession is selected.
 */
enum ConfessionActions: string
{
    case LearnAboutConfession = 'learn_about_confession';
    case ConfessionMenuAction = 'confession_menu_action';
    case ConfessionMenuSubAction = 'confession_menu_sub_action';

}
