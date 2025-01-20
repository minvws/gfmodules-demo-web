<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dto\AuthorizationData;
use App\Dto\ConsentData;
use App\Http\Requests\FlowAuthorizationRequest;
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

    public function storeConsent(FlowConsentRequest $request)
    {
        $data = new ConsentData(
            bsn: $request->validated('bsn'),
            birthYear: $request->validated('birthyear'),
            consent: $request->validated('consent') ? true : false,
        );
        $this->stateService->setConsentDataInSession($data);

        return redirect()->route('flow');
    }

    public function editAuthorization()
    {
        return $this->returnFlowView(
            editConsent: false,
            editAuthorization: true
        );
    }

    public function storeAuthorization(FlowAuthorizationRequest $request)
    {
        $data = new AuthorizationData(
            informationTypes: $request->validated('information_types'),
            accessCode: $request->validated('access_code'),
        );
        $this->stateService->setAuthorizationDataInSession($data);

        return redirect()->route('flow');
    }

    protected function returnFlowView(bool $editConsent = false, bool $editAuthorization = false)
    {
        $state = $this->stateService->getFlowStateFromSession();

        return view('flow.index')
            ->with('state', $state)
            ->with('editConsent', $editConsent)
            ->with('editAuthorization', $editAuthorization)
            ->with('informationTypes', $this->getAvailableInformationTypes());
    }

    protected function getAvailableInformationTypes(): array
    {
        return [
            'imaging' => 'Beeld',
            'medication' => 'Medicatie',
            'acute_care' => 'Acute zorg',
            'e_transfer' => 'eOverdracht',
            'bgz' => 'Basisgegevensset zorg (BGZ)',
        ];
    }
}
