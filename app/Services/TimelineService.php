<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DataDomain;
use App\Models\DeziUser;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class TimelineService
{
    public function findTimeline(
        string $bsn,
        DataDomain $dataDomain,
        DeziUser $deziUser,
        string $caseNumber,
        bool $breakingGlass
    ): array {
        $timelineResponse = $this->requestTimeline($bsn, $dataDomain, $deziUser, $caseNumber, $breakingGlass);
        $errors = $this->catchErrors($timelineResponse);
        [$requested_resources, $patient, $patientName, $references]
            = $this->parseResources($timelineResponse, $dataDomain);
        $requested_resources = $this->sortResourcesByDate($requested_resources, $dataDomain);
        return [
            'errors' => $errors,
            'patient' => $patient,
            'patientName' => $patientName,
            'requested_resources' => $requested_resources,
            'references' => $references,
        ];
    }

    private function catchErrors(
        array $timelineBundle
    ): array {
        $errors = [];
        if (isset($timelineBundle["resourceType"]) && $timelineBundle["resourceType"] == "OperationOutcome") {
            foreach ($timelineBundle['issue'] ?? [] as $issue) {
                $errors[] = [
                    'severity' => $issue['severity'] ?? 'error',
                    'details' =>  'Fout bij opvragen van tijdlijn.',
                    'diagnostics' => $issue['details']['text'] . "\n" . ($issue['diagnostics'] ?? null),
                ];
            }
            return $errors;
        }
        foreach ($this->findResources($timelineBundle, 'OperationOutcome') as $outcome) {
            foreach ($outcome['issue'] ?? [] as $issue) {
                $errors[] = [
                    'severity' => $issue['severity'] ?? 'error',
                    'details' =>  $issue['details']['text'] ?? null,
                    'diagnostics' => $issue['diagnostics'] ?? null,
                ];
            }
        }
        return $errors;
    }

    private function parseResources(array $timelineBundle, DataDomain $dataDomain): array
    {
        $requested_resources = $this->findResources($timelineBundle, $dataDomain->value);
        $patient = $this->findResources($timelineBundle, 'Patient')[0] ?? null;
        $patientName = $patient ? $this->getPatientName($patient) : null;

        $included_references = [
            'ImagingStudy' => ['Patient', 'Organization', 'Practitioner'],
            'MedicationStatement' => ['Patient', 'Organization', 'Practitioner', 'Medication'],
        ];

        $references = [];
        foreach ($included_references[$dataDomain->value] as $ref) {
            $found = $this->findResources($timelineBundle, $ref);
            foreach ($found as $resource) {
                if (isset($resource['id'])) {
                    $references[$ref . '/' . $resource['id']] = $resource;
                }
            }
        }
        return [$requested_resources, $patient, $patientName, $references];
    }

    private function findResources(
        array $bundle,
        string $type,
    ): array {
        $resources = [];
        foreach ($bundle['entry'] ?? [] as $entry) {
            $resource = $entry['resource'] ?? null;
            if (
                $resource &&
                isset($resource['resourceType']) &&
                $resource['resourceType'] === $type
            ) {
                $resources[] = $resource;
            }
        }

        return $resources;
    }

    private function sortResourcesByDate(array $resources, DataDomain $dataDomain): array
    {
        switch ($dataDomain) {
            case DataDomain::ImagingStudy:
                usort($resources, function ($a, $b) {
                    $da = $a['started'] ?? null;
                    $db = $b['started'] ?? null;
                    return $this->nullableDatetime($da) <=> $this->nullableDatetime($db);
                });
                break;
            case DataDomain::MedicationStatement:
                usort($resources, function ($a, $b) {
                    $startA = $a['effectivePeriod']['start'] ?? $a['effectiveDateTime'] ?? null;
                    $startB = $b['effectivePeriod']['start'] ?? $b['effectiveDateTime'] ?? null;
                    return $this->nullableDatetime($startA) <=> $this->nullableDatetime($startB);
                });
                break;
        }
        return $resources;
    }



    private function nullableDatetime(string|null $input): \DateTime | null
    {
        if ($input === null) {
            return null;
        }
        return new \DateTime($input);
    }

    private function getPatientName(array $patient): string
    {
        if (isset($patient['name']) && is_array($patient['name']) && count($patient['name']) > 0) {
            $name = $patient['name'][0];
            $given = isset($name['given']) ? implode(' ', $name['given']) : '';
            $family = isset($name['family']) ? $name['family'] : '';
            return trim($given . ' ' . $family);
        }
        return $patient['display'] ?? 'Onbekend';
    }

    private function requestTimeline(
        string $bsn,
        DataDomain $dataDomain,
        DeziUser $deziUser,
        string $caseNumber,
        bool $breakingGlass
    ): array {
        $client = new Client();

        $resultString = '';

        try {
            $includes = [
                'ImagingStudy' => ['subject', 'performer'],
                'MedicationStatement' => ['subject', 'source', 'medication'],
            ];
            $bsn_identifier_query = "patient.identifier=http://fhir.nl/fhir/NamingSystem/bsn|" . $bsn;
            $resource = $dataDomain->value;

            $include_query = implode(
                '&',
                array_map(fn($field) => '_include=' . $resource . ':' . $field, $includes[$resource])
            );

            $uri = config('timeline.timeline.endpoint') . '/fhir/' . $resource . '/_search?' .
                $bsn_identifier_query . '&' . $include_query;

            $result = $client->request(
                method: 'POST',
                uri: $uri,
                options: [
                    'json' => [
                        'bsn' => $bsn,
                        'ura' => $deziUser->ura ?? '',
                        'dezi_jwt' => $deziUser->getJwt() ?? null,
                        'breakingGlass' => $breakingGlass,
                        'caseNumber' => $caseNumber,
                    ],
                ]
            );
        } catch (ClientException $e) {
            // Handle 4xx errors
            $resultString = $e->getResponse()->getBody()->getContents();
        } catch (ServerException $e) {
            // Handle 5xx errors
            $resultString = $e->getResponse()->getBody()->getContents();
        } catch (\Exception $e) {
            // Handle other errors
            return $this->createOpOutcome(
                'Geen tijdlijn gevonden, probeer het later opnieuw.',
                $e->getMessage()
            );
        }
        if (isset($result)) {
            $resultString = $result->getBody()->getContents();
        }
        $decoded = json_decode($resultString, true);

        if (!is_array($decoded)) {
            return $this->createOpOutcome(
                'Geen tijdlijn gevonden, probeer het later opnieuw.',
                'Lege of ongeldige response ontvangen van timeline service.'
            );
        }

        return $decoded;
    }

    private function createOpOutcome(string $details, string $diagnostics): array
    {
        return [
            'resourceType' => 'OperationOutcome',
            'issue' => [[
                'severity' => 'error',
                'details' => ['text' => $details],
                'diagnostics' => $diagnostics,
            ]],
        ];
    }
}
