<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;

class AddressingService
{
    public function findOrganization(string $id, bool $includeEndpoints = true): array
    {
        $query = [];
        if ($includeEndpoints) {
            $query['_include'] = 'Organization.endpoint';
        }

        $client = new Client();
        $result = $client->request('GET', config('addressing.endpoint') . "/Organization/_search/{$id}", [
            'query' => $query,
            'headers' => [
                'accept' => 'application/json',
            ],
        ]);
        $data = json_decode($result->getBody()->getContents(), true);

        // Iterate searchbundle and find organization and endpoints
        $ret = [];
        foreach ($data['entry'] as $entry) {
            if ($entry['resource']['resourceType'] === 'Organization') {
                $ret['organization'] = $entry['resource'];
            }
            if ($entry['resource']['resourceType'] === 'Endpoint') {
                $ret['endpoints'][] = $entry['resource'];
            }
        }

        return $ret;
    }
}
