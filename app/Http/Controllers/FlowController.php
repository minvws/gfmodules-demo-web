<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dto\ConsentData;
use App\Http\Requests\FlowConsentRequest;
use App\Services\FlowStateService;

class FlowController extends Controller
{
    public function __construct(protected FlowStateService $stateService)
    {
    }

    public function index()
    {
        return $this->returnFlowView();
    }

    public function editConsent()
    {
        return $this->returnFlowView(editConsent: true);
    }

    public function saveConsent(FlowConsentRequest $request)
    {
        $data = new ConsentData(
            bsn: $request->validated('bsn'),
            birthYear: $request->validated('birthyear'),
            consent: $request->validated('consent') ? true : false,
        );
        $this->stateService->setConsentDataInSession($data);

        return redirect()->route('flow');
    }

    protected function returnFlowView($editConsent = false)
    {
        $state = $this->stateService->getFlowStateFromSession();

        return view('flow.index')
            ->with('state', $state)
            ->with('editConsent', $editConsent);
    }
}
