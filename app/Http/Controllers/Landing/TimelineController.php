<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Services\AddressingService;
use App\Services\FlowStateService;
use App\Services\TimelineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimelineController extends Controller
{
    public function orgInfo(Request $request, string $ref, AddressingService $addressingService): View
    {
        $org = $addressingService->findOrganization($ref, includeEndpoints: true);
        return view('timeline.org_info')
            ->with('organization', $org['organization'])
            ->with('endpoints', $org['endpoints'] ?? [])
        ;
    }

    public function fetch(
        FlowStateService $stateService,
        TimelineService $timelineService,
    ): View|RedirectResponse {
        $flowState = $stateService->getFlowStateFromSession();
        if (!$flowState->isFlowComplete()) {
            return redirect()->route('flow');
        }

        $bsn = $flowState->getConsentData()?->getBsn();
        $informationTypes = $flowState->getAuthorizationData()?->getInformationTypes() ?? [];
        if (empty($informationTypes)) {
            return redirect()->route('flow');
        }

        $dataDomain = $informationTypes[0];

        $timelineBundle = $timelineService->findTimeline($bsn, $dataDomain);

        $patient = null;

        // Convert timeline bundle to a list of series
        $imagingStudiesSeries = [];
        $medicationStatements = [];
        $errors = [];

        if ($timelineBundle['detail'] && $timelineBundle['detail']['resourceType'] === 'OperationOutcome') {
            foreach ($timelineBundle['detail']['issue'] ?? [] as $issue) {
                $errors[] = [
                    'severity' => $issue['severity'],
                    'details' => $issue['details']['text']
                ];
            }
        }

        foreach ($timelineBundle['entry'] ?? [] as $searchSet) {
            $meta = $searchSet['resource']['entry'][1];
            $addressingInformation = $this->getAddressingInformation($searchSet);
            if ($meta['resource']['resourceType'] === "OperationOutcome") {
                foreach ($meta['resource']['issue'] ?? [] as $issue) {
                    $errors[] = [
                        'severity' => $issue['severity'],
                        'details' => $issue['details']['text']
                    ];
                }
            }

            foreach ($meta['resource']['entry'] ?? [] as $entry) {
                if ($entry['resource']['resourceType'] === "ImagingStudy") {
                    // Set patient if not set yet
                    if (!$patient) {
                        $patient = $this->getPatient($meta, $entry);
                    }

                    foreach ($entry['resource']['series'] as $resource) {
                        $imagingStudiesSeries[] = [
                            'resource' => $resource,
                            'references' => [
                                'organization' => $this->getOrganisation($meta, $resource),
                                'practitioner' => $this->getPractitioner($meta, $resource),
                                'patient' => $this->getPatient($meta, $entry),
                                'addressingInformation' => $this->parseAddressingInformation($addressingInformation),
                            ]
                        ];
                    }
                }
                if ($entry['resource']['resourceType'] === "MedicationStatement") {
                    if (!$patient) {
                        $patient = $this->getPatient($meta, $entry);
                    }

                    $medicationStatements[] = [
                        'resource' => $entry['resource'],
                        'references' => [
                            'addressingInformation' => $this->parseAddressingInformation($addressingInformation),
                        ]
                    ];
                }
            }
        }

        // sort series by started
        usort($imagingStudiesSeries, function ($a, $b) {
            $da = new \DateTime($a['resource']['started']);
            $db = new \DateTime($b['resource']['started']);
            return $db <=> $da;
        });

        // sort medicationstatement ['resource']['effectivePeriod']['start'] by earliest started first
        usort($medicationStatements, function ($a, $b) {
            $da = new \DateTime($a['resource']['effectivePeriod']['start']);
            $db = new \DateTime($b['resource']['effectivePeriod']['start']);
            return $da <=> $db;
        });

        if ($dataDomain->value === 'ImagingStudy') {
            return view('timeline.imagingstudy_result')
                ->with('bsn', $bsn)
                ->with('patient', $patient)
                ->with('series', $imagingStudiesSeries)
                ->with('errors', $errors)
            ;
        }
        return view('timeline.medicationstatement_result')
            ->with('bsn', $bsn)
            ->with('patient', $patient)
            ->with('medicationStatements', $medicationStatements)
            ->with('errors', $errors)
        ;
    }

    protected function parseAddressingInformation(array $addressingInformation): array
    {
        return [
            'ura' => $this->getUra($addressingInformation),
            'name' => $this->getAddressingName($addressingInformation),
            'endpoint' => $this->getAddressingEndpoint($addressingInformation),
            'organizationId' => $this->getAddressingOrganizationId($addressingInformation),
        ];
    }

    protected function getAddressingInformation(array $searchSet): array
    {
        // TODO: As long as we're not using a FHIR extension to define the setup of the bundle
        // we're bound to fixed indexes to tind the resource.
        return $searchSet['resource']['entry'][0]['resource'];
    }

    protected function getUra(array $addressingInformation): string
    {
        // TODO: As long as we're not using a FHIR extension to define the setup of the bundle
        // we're bound to fixed indexes to tind the resource.
        return $addressingInformation['entry'][0]['resource']['identifier'][1]['value'];
    }

    protected function getAddressingName(array $addressingInformation): string
    {
        // TODO: As long as we're not using a FHIR extension to define the setup of the bundle
        // we're bound to fixed indexes to tind the resource.
        return $addressingInformation['entry'][0]['resource']['name'];
    }

    protected function getAddressingEndpoint(array $addressingInformation): string
    {
        // TODO: As long as we're not using a FHIR extension to define the setup of the bundle
        // we're bound to fixed indexes to tind the resource.
        return $addressingInformation['entry'][1]['resource']['address'];
    }

    protected function getAddressingOrganizationId(array $addressingInformation): string
    {
        // TODO: As long as we're not using a FHIR extension to define the setup of the bundle
        // we're bound to fixed indexes to tind the resource.
        return $addressingInformation['entry'][0]['resource']['id'];
    }

    public function getPatient(array $bundle, array $resource): array
    {
        if (array_key_exists('display', $resource['resource']['subject'])) {
            return [
                'display' => $resource['resource']['subject']['display']
            ];
        }
        list($resourceType, $reference) = explode('/', $resource['resource']['subject']['reference'], 2);
        return $this->findResource($bundle, $resourceType, $reference);
    }

    public function getPractitioner(array $bundle, array $resource): array
    {
        return $this->getPerformer($bundle, $resource, "Practitioner");
    }

    public function getOrganisation(array $bundle, array $resource): array
    {
        return $this->getPerformer($bundle, $resource, "Organization");
    }

    public function getPerformer(array $bundle, array $resource, string $type): array
    {
        foreach ($resource['performer'] ?? [] as $performer) {
            if ($performer['actor']['type'] === $type) {
                if (array_key_exists("display", $performer['actor'])) {
                    return [
                        'reference' => $performer['actor']['reference'],
                        'name' => $performer['actor']['display'],
                    ];
                }
                list($resourceType, $reference) = explode('/', $performer['actor']['reference'], 2);
                return $this->findResource($bundle, $resourceType, $reference);
            }
        }

        return [];
    }

    protected function findResource(array $bundle, string $type, string $id): array
    {
        foreach ($bundle['resource']['entry'] as $entry) {
            if ($entry['resource']['id'] === $id && $entry['resource']['resourceType'] === $type) {
                return $entry['resource'];
            }
        }

        return [];
    }
}
