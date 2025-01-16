<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\FlowStateService;

class FlowController extends Controller
{
    public function __construct(protected FlowStateService $stateService)
    {
    }

    public function index()
    {
        $state = $this->stateService->getFlowStateFromSession();

        return view('flow.index')
            ->with('state', $state);
    }
}
