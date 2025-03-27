<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\AddressBookSearchValues;
use GuzzleHttp\Client;

class AddressingService
{
    public function findOrganization(string $id, bool $includeEndpoints = true): array
    {
        $query = [];
        if ($includeEndpoints) {
            $query['_id'] = $id;
            $query['_include'] = 'Organization:endpoint';
        }

        $client = new Client();
        $result = $client->request('GET', config('addressing.endpoint') . "/Organization/_search", [
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

    public function findOrganizations(AddressBookSearchValues $searchValues): array
    {
        $query = [];
        $query['_include'] = 'Organization:endpoint';
        if (!empty($searchValues->name)) {
            $query['name'] = $searchValues->name;
        }
        if (!empty($searchValues->ura)) {
            $query['identifier'] = $searchValues->ura;
        }

        $client = new Client();
        $result = $client->request('GET', config('addressing.endpoint') . "/Organization/_search", [
            'query' => $query,
            'headers' => [
                'accept' => 'application/json',
            ],
        ]);
        $data = json_decode($result->getBody()->getContents(), true);

        // Iterate searchbundle and find organizations and endpoints
        $organizations = [];
        $endpoints = [];

        if (empty($data['entry'])) {
            return [$organizations, $endpoints];
        }

        foreach ($data['entry'] as $entry) {
            if ($entry['resource']['resourceType'] === 'Organization') {
                $organizations[] = $entry['resource'];
            }
            if ($entry['resource']['resourceType'] === 'Endpoint') {
                $endpoints[] = $entry['resource'];
            }
        }

        return [$organizations, $endpoints];
    }
}
