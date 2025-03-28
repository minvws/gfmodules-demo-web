<?php

declare(strict_types=1);

namespace App\Dto;

readonly class OrganizationsListResult
{
    public function __construct(
        public array $organizations,
        public AddressBookSearchValues $searchValues,
        public ?int $total = null,
        public ?array $previousPageQuery = null,
        public ?array $nextPageQuery = null,
    ) {
    }

    public function hasPreviousPage(): bool
    {
        return $this->previousPageQuery !== null;
    }

    public function hasNextPage(): bool
    {
        return $this->nextPageQuery !== null;
    }
}
