<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enums\DataDomain;

class AuthorizationData
{
    /**
     * @var DataDomain[]
     */
    protected array $informationTypes = [];

    /**
     * @param DataDomain[] $informationTypes
     */
    public function __construct(
        array $informationTypes = [],
    ) {
        $this->informationTypes = $informationTypes;
    }

    /**
     * @return DataDomain[]
     */
    public function getInformationTypes(): array
    {
        return $this->informationTypes;
    }
}
