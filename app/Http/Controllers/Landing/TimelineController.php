<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Services\FlowStateService;
use App\Services\TimelineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Enums\ConsentType;

class TimelineController extends Controller
{
    public function fetch(
        FlowStateService $stateService,
        TimelineService $timelineService,
    ): View|RedirectResponse {
        $flowState = $stateService->getFlowStateFromSession();
        if (!$flowState->isFlowComplete()) {
            return redirect()->route('flow');
        }

        $bsn = $flowState->getConsentData()?->getBsn();
        $authorizationData = $flowState->getAuthorizationData();
        $resource_types = $authorizationData?->getInformationTypes() ?? [];
        $user = $flowState->getUser();

        if (empty($bsn) || empty($resource_types) || $user === null) {
            return redirect()->route('flow');
        }

        // Generate a case number since this feature is not in the viewer
        $caseNr = 'CASE_' . time() . '_' . substr(md5($bsn . $user->uziId), 0, 8);
        $breakingGlass = $flowState->getConsentData()?->getConsentType() === ConsentType::BreakingGlass;

        $dataDomain = $resource_types[0];
        $timelineResults = $timelineService->findTimeline($bsn, $dataDomain, $user, $caseNr, $breakingGlass);

        return view('timeline.result')
            ->with('bsn', $bsn)
            ->with('patient', $timelineResults['patient'])
            ->with('patientName', $timelineResults['patientName'])
            ->with('requested_resources', $timelineResults['requested_resources'])
            ->with('references', $timelineResults['references'])
            ->with('errors', $timelineResults['errors']);
    }
}
