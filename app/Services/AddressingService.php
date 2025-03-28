<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\AddressBookSearchValues;
use App\Dto\OrganizationsListResult;
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

    public function findOrganizations(AddressBookSearchValues $searchValues): OrganizationsListResult
    {
        $query = [
            '_include' => 'Organization:endpoint',
            '_count' => $searchValues->count,
            '_getpagesoffset' => $searchValues->offset,
        ];
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

        return $this->buildOrganizationListResult($data, $searchValues);
    }

    protected function buildOrganizationListResult(
        array $data,
        AddressBookSearchValues $searchValues,
    ): OrganizationsListResult {
        $organizations = [];
        $previousPageQuery = $this->buildSearchQueryParams('previous', $data, $searchValues);
        $nextPageQuery = $this->buildSearchQueryParams('next', $data, $searchValues);
        $total = $data['total'] ?? null;

        if (empty($data['entry'])) {
            return new OrganizationsListResult(
                organizations: [],
                searchValues: $searchValues,
                total: $total,
                previousPageQuery: $previousPageQuery,
                nextPageQuery: $nextPageQuery,
            );
        }

        // Iterate searchbundle and find organizations
        foreach ($data['entry'] as $entry) {
            if ($entry['resource']['resourceType'] === 'Organization') {
                $organizations[] = $entry['resource'];
            }
        }

        return new OrganizationsListResult(
            organizations: $organizations,
            searchValues: $searchValues,
            total: $total,
            previousPageQuery: $previousPageQuery,
            nextPageQuery: $nextPageQuery,
        );
    }

    protected function resultHasLink(array $data, string $relation): bool
    {
        foreach ($data['link'] as $link) {
            if ($link['relation'] === $relation) {
                return true;
            }
        }

        return false;
    }

    protected function buildSearchQueryParams(string $type, array $data, AddressBookSearchValues $searchValues): ?array
    {
        $query = [
            'name' => $searchValues->name,
            'ura' => $searchValues->ura,
//            '_count' => $searchValues->count, // Enable when we want the user to be able to change the count
        ];

        if ($type === 'previous' && $this->resultHasLink($data, 'previous')) {
            return array_filter([
                ...$query,
                '_getpagesoffset' => $searchValues->offset - $searchValues->count,
            ]);
        }

        if ($type === 'next' && $this->resultHasLink($data, 'next')) {
            return array_filter([
                ...$query,
                '_getpagesoffset' => $searchValues->offset + $searchValues->count,
            ]);
        }

        return null;
    }
}
