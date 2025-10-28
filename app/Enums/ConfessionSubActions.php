<?php

namespace App\Enums;

/**
 * Detailed liturgical actions available within the ConfessionMenuAction.
 */
enum ConfessionSubActions: string
{
    /**
     * Замовлення Сорокоусту (молитовна згадка про здоров'я чи упокій протягом 40 днів).
     */
    case Sorokoust = 'sorokoust';

    /**
     * Поставити свічку онлайн.
     */
    case LightACandle = 'light_a_candle';

    /**
     * Подати записку (за здоров'я або за упокій) для поминання в храмі.
     */
    case SubmitPrayerNote = 'submit_prayer_note';

    /**
     * Замовлення читання Акафістів.
     */
    case ReadAkathists = 'read_akathists';

    /**
     * Замовлення читання Неусипаної Псалтирі (цілодобове поминання).
     */
    case ReadUnceasingPsalter = 'read_unceasing_psalter';

    /**
     * Замовлення Панахиди (заупокійне богослужіння).
     */
    case MemorialService = 'memorial_service';
}
