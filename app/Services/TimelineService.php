<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DataDomain;
use App\Dto\AuthorizationData;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class TimelineService
{
    public function findTimeline(string $bsn, DataDomain $dataDomain, string $accessCode): array
    {
        $client = new Client();

        try {
            $result = $client->request(
                method: 'POST',
                uri: config('timeline.timeline.endpoint') . '/fhir/' . $dataDomain->value . '/_search',
                options: [
                    'query' => [
                        'bsn' => $bsn,
                        'authorization_token' => $accessCode,
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
