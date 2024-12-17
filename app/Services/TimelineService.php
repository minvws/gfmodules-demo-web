<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;

class TimelineService
{
    public function __construct(
        private BsnService $bsnService,
    ) {
    }

    public function findTimeline(string $bsn, string $dataDomain): array
    {
        $client = new Client();

        $hashedBsn = $this->bsnService->hashBsn($bsn);

        $result = $client->request('POST', config('timeline.pseudonym.endpoint') . '/register', [
            'json' => [
                'provider_id' => config('timeline.timeline.provider_id'),
                'bsn_hash' => $hashedBsn,
            ],
        ]);
        $data = json_decode($result->getBody()->getContents(), true);
        $pseudonym = $data['pseudonym'] ?? '';

        $result = $client->request('POST', config('timeline.timeline.endpoint') . '/fhir/ImagingStudy/_search', [
            'query' => [
                'pseudonym' => $pseudonym
            ],
        ]);
        return json_decode($result->getBody()->getContents(), true);
    }
}
