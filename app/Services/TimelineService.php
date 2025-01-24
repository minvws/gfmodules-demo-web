<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DataDomain;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class TimelineService
{
    public function __construct(
        private BsnService $bsnService,
    ) {
    }

    public function findTimeline(string $bsn, DataDomain $dataDomain): array
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

        try {
            $result = $client->request(
                method: 'POST',
                uri: config('timeline.timeline.endpoint') . '/fhir/' . $dataDomain->value . '/_search',
                options: [
                    'query' => [
                        'pseudonym' => $pseudonym
                    ],
                ]
            );

            return json_decode($result->getBody()->getContents(), true);
        } catch (ClientException $e) {
            // Handle 4xx errors
            return json_decode($e->getResponse()->getBody()->getContents(), true);
        }
    }
}
