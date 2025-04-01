<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;

class CompanyKvkIdentifier extends Component
{
    protected const KVK_IDENTIFIER = 'http://example.com/fhir/NamingSystem/kvk';

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
        $filterFunction = static fn($identifier) => ($identifier['system'] ?? '') === self::KVK_IDENTIFIER;

        $uraIdentifier = array_values(array_filter($this->identifiers ?? [], $filterFunction))[0] ?? [];
        return $uraIdentifier['value'] ?? '';
    }
}
