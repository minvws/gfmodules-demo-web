<?php

declare(strict_types=1);

namespace App\Services\Dezi;

enum OidcUserLoa: string
{
    case BASIC = 'http://eid.logius.nl/LoA/basic';
    case LOW = 'http://eidas.europa.eu/LoA/low';
    case SUBSTANTIAL = 'http://eidas.europa.eu/LoA/substantial';
    case HIGH = 'http://eidas.europa.eu/LoA/high';

    public function level(): int
    {
        return match ($this) {
            self::BASIC => 0,
            self::LOW => 1,
            self::SUBSTANTIAL => 2,
            self::HIGH => 3,
        };
    }

    public static function isEqualOrHigher(self $minimumLoa, self $loa): bool
    {
        return $loa->level() >= $minimumLoa->level();
    }
}
