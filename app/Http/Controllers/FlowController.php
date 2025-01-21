<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dto\AuthorizationData;
use App\Dto\ConsentData;
use App\Enums\DataDomain;
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

    public function retrieveTimeline()
    {
        $flowState = $this->stateService->getFlowStateFromSession();
        if (!$flowState->isFlowComplete()) {
            return redirect()->route('flow');
        }

        return redirect()->route('timeline.fetch');
    }

    public function editConsent()
    {
        return $this->returnFlowView(editConsent: true);
    }

    public function storeConsent(FlowConsentRequest $request)
    {
        $data = new ConsentData(
            bsn: $request->validated('bsn'),
            //            birthYear: $request->validated('birthyear'),
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
            informationTypes: DataDomain::fromStringArray($request->validated('information_types')),
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
            DataDomain::ImagingStudy->value => 'Beeld',
            DataDomain::MedicationStatement->value => 'Medicatie',
            DataDomain::AcuteCare->value => 'Acute zorg',
            DataDomain::ETransfer->value => 'eOverdracht',
            DataDomain::BGZ->value => 'Basisgegevensset zorg (BGZ)',
        ];
    }
}
