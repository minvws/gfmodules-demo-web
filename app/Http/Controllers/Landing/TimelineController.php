<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Services\AddressingService;
use App\Services\BsnService;
use App\Services\TimelineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimelineController extends Controller
{
    public function home(Request $request): View
    {
        return view('timeline.home')->with('user', $request->user());
    }

    public function orgInfo(Request $request, string $ref, AddressingService $addressingService): View
    {
        $org = $addressingService->findOrganization($ref, includeEndpoints: true);
        return view('timeline.org_info')
            ->with('organization', $org['organization'])
            ->with('endpoints', $org['endpoints'] ?? [])
        ;
    }

    public function fetch(
        Request $request,
        BsnService $bsnService,
        TimelineService $timelineService
    ): View|RedirectResponse {
        $bsn = strval($request->request->get('bsn'));
        if (!$bsn) {
            return redirect()->route('timeline.home');
        }
        if (!$bsnService->isValid($bsn)) {
            return redirect()->route('timeline.home')->with('error', 'Invalid BSN');
        }

        $dataDomain = strval($request->request->get('data_domain'));
        $timelineBundle = $timelineService->findTimeline($bsn, $dataDomain);

        $patient = null;

        // Convert timeline bundle to a list of series
        $imagingStudiesSeries = [];
        $errors = [];
        foreach ($timelineBundle['entry'] ?? [] as $searchSet) {
            $meta = $searchSet['resource']['entry'][1];
            $addressingInformation = $this->getAddressingInformation($searchSet);
            if ($meta['resource']['resourceType'] === "OperationOutcome") {
                foreach ($meta['resource']['issue'] ?? [] as $issue) {
                    array_push($errors, [
                        'severity' => $issue['severity'],
                        'details' => $issue['details']['text']
                    ]);
                }
            }

            foreach ($meta['resource']['entry'] ?? [] as $entry) {
                if ($entry['resource']['resourceType'] !== "ImagingStudy") {
                    continue;
                }

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
                            'addressingInformation' => [
                                'ura' => $this->getUra($addressingInformation),
                                'name' => $this->getAddressingName($addressingInformation),
                                'endpoint' => $this->getAddressingEndpoint($addressingInformation)
                            ],
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

        return view('timeline.result')
            ->with('bsn', $bsn)
            ->with('patient', $patient)
            ->with('series', $imagingStudiesSeries)
            ->with('errors', $errors)
        ;
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
