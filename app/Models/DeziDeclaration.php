<?php

declare(strict_types=1);

namespace App\Models;

class DeziDeclaration
{
    public function __construct(
        public string $loaDezi,
        public string $declarationId,
        public string $deziNumber,
        public string $initials,
        public string $surnamePrefix,
        public string $surname,
        public string $subscriberNumber,
        public string $subscriberName,
        public string $roleCode,
        public string $roleName,
        public string $roleCodeSource,
    ) {
    }
}
