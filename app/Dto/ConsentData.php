<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enums\ConsentType;

class ConsentData
{
    protected ?string $bsn = null;
    protected ?string $birthYear = null;
    protected ?ConsentType $consentType = null;

    public function __construct(
        ?string $bsn = null,
        ?string $birthYear = null,
        ?ConsentType $consentType = null
    ) {
        $this->bsn = $bsn;
        $this->birthYear = $birthYear;
        $this->consentType = $consentType;
    }

    public function getBsn(): ?string
    {
        return $this->bsn;
    }

    public function getBirthYear(): ?string
    {
        return $this->birthYear;
    }

    public function getConsentType(): ?ConsentType
    {
        return $this->consentType;
    }
}
