<?php

declare(strict_types=1);

namespace App\Dto;

readonly class AddressBookSearchValues
{
    public function __construct(
        public ?string $name,
        public ?string $ura,
        public ?int $offset = 0,
        public ?int $count = 20,
    ) {
    }
}
