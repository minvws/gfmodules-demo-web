<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\FlowState;
use App\Services\Uzi\UziAuthGuard;
use Illuminate\Contracts\Session\Session;

class FlowStateService
{
    public function __construct(
        protected UziAuthGuard $uziAuthGuard,
        protected Session $session,
    ) {
    }

    public function getFlowStateFromSession(): FlowState
    {
        $user = $this->uziAuthGuard->user();

        return new FlowState(user: $user);
    }
}
