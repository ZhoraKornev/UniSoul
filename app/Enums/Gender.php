<?php

namespace App\Enums;

enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case OTHER = 'other';

    /**
     * Get the emoji representation of the gender.
     */
    public function emoji(): string
    {
        return match ($this) {
            self::MALE => '♂️',
            self::FEMALE => '♀️',
            self::OTHER => '⚧️',
        };
    }

    /**
     * Get the translated label for the gender.
     */
    public function label(): string
    {
        return match ($this) {
            self::MALE => trans('telegram.gender.male'),
            self::FEMALE => trans('telegram.gender.female'),
            self::OTHER => trans('telegram.gender.other'),
        };
    }
}
