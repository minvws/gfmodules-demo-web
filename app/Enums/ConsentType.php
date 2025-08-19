<?php

declare(strict_types=1);

namespace App\Enums;

enum ConsentType: string
{
    case TreatmentRelation = 'treatment_relation';
    case BreakingGlass = 'breaking_glass';

    /**
     * @param string[] $consentTypes
     * @return self[]
     */
    public static function fromStringArray(array $consentTypes): array
    {
        return array_map(static fn(string $consentType) => self::from($consentType), $consentTypes);
    }

    /**
     * @param self[] $consentTypes
     * @return string[]
     */
    public static function toStringArray(array $consentTypes): array
    {
        return array_map(static fn(self $consentType) => $consentType->value, $consentTypes);
    }
}
