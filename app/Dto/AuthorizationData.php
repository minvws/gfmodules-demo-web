<?php

declare(strict_types=1);

namespace App\Dto;

class AuthorizationData
{
    protected array $informationTypes = [];
    protected ?string $accessCode = null;

    public function __construct(
        array $informationTypes = [],
        ?string $accessCode = null
    ) {
        $this->informationTypes = $informationTypes;
        $this->accessCode = $accessCode;
    }

    public function getInformationTypes(): array
    {
        return $this->informationTypes;
    }

    public function getAccessCode(): ?string
    {
        return $this->accessCode;
    }
}
