<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DataDomain;
use App\Models\UziUser;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class TimelineService
{
    public function findTimeline(
        string $bsn,
        DataDomain $dataDomain,
        UziUser $uziUser,
        string $caseNumber,
        bool $breakingGlass
    ): array {
        $client = new Client();

        try {
            $result = $client->request(
                method: 'POST',
                uri: config('timeline.timeline.endpoint') . '/fhir/' . $dataDomain->value . '/_search',
                options: [
                    'json' => [
                        'bsn' => $bsn,
                        'ura' => $uziUser->getFirstRelation()->ura ?? '',
                        'dezi_jwt' => $uziUser->getJwt() ?? null,
                        'breakingGlass' => $breakingGlass,
                        'caseNumber' => $caseNumber,
                    ],
                ]
            );

            return json_decode($result->getBody()->getContents(), true);
        } catch (ClientException $e) {
            // Handle 4xx errors
            return json_decode($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            // Handle other errors
            return [];
        }
    }
}
