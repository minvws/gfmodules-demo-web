<?php

declare(strict_types=1);

namespace App\View\Components;

use Carbon\Carbon;
use Illuminate\View\Component;

class CompanyLastUpdated extends Component
{
    public function __construct(
        protected ?array $meta = null,
    ) {
        //
    }

    public function render(): string
    {
        $lastUpdated = $this->getLastUpdated();
        if (!$lastUpdated) {
            return '';
        }

        return Carbon::parse($lastUpdated)->format('d-m-Y H:i:s');
    }

    protected function getLastUpdated(): ?string
    {
        return $this->meta['lastUpdated'] ?? null;
    }
}
