<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Enums\DataDomain;
use App\Http\Controllers\Controller;
use App\Services\AddressingService;
use App\Services\FlowStateService;
use App\Services\TimelineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TimelineController extends Controller
{
    public function orgInfo(string $ref, AddressingService $addressingService): View
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
        $authorizationData = $flowState->getAuthorizationData();
        $informationTypes = $authorizationData?->getInformationTypes() ?? [];
        $accessCode = $authorizationData?->getAccessCode();

        if (empty($bsn) || empty($informationTypes) || empty($accessCode)) {
            return redirect()->route('flow');
        }

        $dataDomain = $informationTypes[0];
        $timelineBundle = $timelineService->findTimeline($bsn, $dataDomain, $accessCode);

        $patient = null;
        $imagingStudiesSeries = [];
        $medicationStatements = [];
        $errors = [];

        $this->processTimelineBundle(
            $timelineBundle,
            $dataDomain,
            $patient,
            $imagingStudiesSeries,
            $medicationStatements,
            $errors
        );
        usort($imagingStudiesSeries, function ($a, $b) {
            $da = new \DateTime($a['resource']['started']);
            $db = new \DateTime($b['resource']['started']);
            return $db <=> $da;
        });

        usort($medicationStatements, function ($a, $b) {
            $startA = $a['resource']['effectivePeriod']['start'] ?? $a['resource']['effectiveDateTime'] ?? null;
            $startB = $b['resource']['effectivePeriod']['start'] ?? $a['resource']['effectiveDateTime'] ?? null;

            return $this->nullableDatetime($startA) <=> $this->nullableDatetime($startB);
        });

        if ($dataDomain->value === 'ImagingStudy') {
            return view('timeline.imagingstudy_result')
                ->with('bsn', $bsn)
                ->with('patient', $patient)
                ->with('series', $imagingStudiesSeries)
                ->with('errors', $errors);
        }
        return view('timeline.medicationstatement_result')
            ->with('bsn', $bsn)
            ->with('patient', $patient)
            ->with('medicationStatements', $medicationStatements)
            ->with('errors', $errors);
    }

    private function processTimelineBundle(
        array $timelineBundle,
        DataDomain $dataDomain,
        ?array &$patient,
        array &$imagingStudiesSeries,
        array &$medicationStatements,
        array &$errors
    ): void {
        $find_through_careplans = $dataDomain->value === 'MedicationStatement';

        if (isset($timelineBundle['detail']) && $timelineBundle['detail']['resourceType'] === 'OperationOutcome') {
            foreach ($timelineBundle['detail']['issue'] ?? [] as $issue) {
                $errors[] = [
                    'severity' => $issue['severity'],
                    'details' => $issue['details']['text']
                ];
            }
        }

        foreach ($timelineBundle['entry'] ?? [] as $searchSet) {
            $meta = $searchSet['resource']['entry'][1];
            if ($meta['resource']['resourceType'] === "OperationOutcome") {
                foreach ($meta['resource']['issue'] ?? [] as $issue) {
                    $errors[] = [
                        'severity' => $issue['severity'],
                        'details' => $issue['details']['text']
                    ];
                }
            }
            $addressingInformation = [];
            if (!$find_through_careplans) {
                $addressingInformation = $this->getAddressingInformation($searchSet);
            }

            foreach ($meta['resource']['entry'] ?? [] as $provider_entry) {
                if (!$find_through_careplans) {
                    $this->processEntry(
                        $meta,
                        $provider_entry,
                        $patient,
                        $imagingStudiesSeries,
                        $medicationStatements,
                        $addressingInformation
                    );
                    continue;
                }

                $addressingInformation = $this->getAddressingInformation($provider_entry);
                $outcomeCheck = $provider_entry['resource']['entry'][1]['resource'];

                if (
                    array_key_exists('resourceType', $outcomeCheck) &&
                    $outcomeCheck['resourceType'] === "OperationOutcome"
                ) {
                    foreach ($provider_entry['resource']['entry'][1]['resourceType'] ?? [] as $issue) {
                        $errors[] = [
                            'severity' => $issue['severity'],
                            'details' => $issue['details']['text']
                        ];
                    }
                    continue;
                }

                $entries = $provider_entry['resource']['entry'][1]['resource']['entry'];
                foreach ($entries ?? [] as $entry) {
                    $this->processEntry(
                        $meta,
                        $entry,
                        $patient,
                        $imagingStudiesSeries,
                        $medicationStatements,
                        $addressingInformation
                    );
                }
            }
        }
    }

    private function processEntry(
        array $meta,
        array $entry,
        ?array &$patient,
        array &$imagingStudiesSeries,
        array &$medicationStatements,
        array $addressingInformation
    ): void {
        if ($entry['resource']['resourceType'] === "ImagingStudy") {
            if (!$patient) {
                $patient = $this->getPatient($meta, $entry);
            }
            $imagingStudiesSeries = array_merge(
                $imagingStudiesSeries,
                $this->getImagingStudySeries($meta, $entry, $addressingInformation)
            );
        }
        if ($entry['resource']['resourceType'] === "MedicationStatement") {
            if (!$patient) {
                $patient = $this->getPatient($meta, $entry);
            }
            $medicationStatements[] = $this->getMedicationStatement($entry, $addressingInformation);
        }
    }

    protected function getImagingStudySeries(array $meta, array $entry, array $addressingInformation): array
    {
        $imagingStudiesSeries = [];
        // Set patient if not set yet
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
        return $imagingStudiesSeries;
    }

    protected function getMedicationStatement(array $entry, array $addressingInformation): array
    {

        return [
            'resource' => $entry['resource'],
            'references' => [
                'addressingInformation' => $this->parseAddressingInformation($addressingInformation),
            ]
        ];
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
        foreach ($addressingInformation['entry'][0]['resource']['identifier'] as $ura) {
            if ($ura['system'] === 'http://fhir.nl/fhir/NamingSystem/ura') {
                return $ura['value'];
            }
        }
        return '#';
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

    private function nullableDatetime(string|null $input): \DateTime | null
    {
        if ($input === null) {
            return null;
        }
        return new \DateTime($input);
    }
}
