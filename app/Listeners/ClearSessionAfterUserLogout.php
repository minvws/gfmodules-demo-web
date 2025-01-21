<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Services\FlowStateService;
use Illuminate\Auth\Events\Logout;

class ClearSessionAfterUserLogout
{
    /**
     * Create the event listener.
     */
    public function __construct(protected FlowStateService $stateService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        $this->stateService->clearFlowState();
    }
}
