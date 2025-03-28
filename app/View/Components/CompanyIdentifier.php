<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;

class CompanyIdentifier extends Component
{
    protected const URA_IDENTIFIER = 'http://fhir.nl/fhir/NamingSystem/ura';

    public function __construct(
        protected ?array $identifiers = null,
    ) {
        //
    }

    public function render(): string
    {
        return $this->getIdentifier();
    }

    protected function getIdentifier(): string
    {
        $filterFunction = static fn($identifier) => ($identifier['system'] ?? '') === self::URA_IDENTIFIER;

        $uraIdentifier = array_values(array_filter($this->identifiers ?? [], $filterFunction))[0] ?? [];
        return $uraIdentifier['value'] ?? '';
    }
}
