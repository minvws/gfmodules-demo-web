<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Container\Attributes\Config;
use Illuminate\Container\Attributes\Give;

class NviService
{
    protected const OAUTH_SCOPE_READ = 'nvi:read';

    protected const OAUTH_SCOPE_WRITE = 'nvi:write';

    protected const OAUTH_SCOPE_DELETE = 'nvi:delete';

    protected const OAUTH_SCOPE_LOCALIZE = 'nvi:localize';

    public function __construct(
        #[Give('gfmodules.nvi_client')]
        protected Client $nviClient,
        #[Give('gfmodules.nvi_oauth_client')]
        protected Client $oauthClient,
        #[Config('gfmodules.nvi.url')]
        protected string $nviUrl,
        #[Config('gfmodules.nvi.subject_identifier_system')]
        protected string $subjectIdentifierSystem,
        #[Config('gfmodules.nvi.custodian_extension_url')]
        protected string $custodianExtensionUrl,
        #[Config('gfmodules.nvi.custodian_identifier_system')]
        protected string $custodianIdentifierSystem,
        #[Config('gfmodules.nvi.custodian_identifier_value')]
        protected string $custodianIdentifierValue,
        #[Config('gfmodules.nvi.source_identifier_system')]
        protected string $sourceIdentifierSystem,
        #[Config('gfmodules.nvi.source_identifier_value')]
        protected string $sourceIdentifierValue,
        #[Config('gfmodules.nvi.list_code')]
        protected string $listCode,
        #[Config('gfmodules.nvi.list_code_system')]
        protected string $listCodeSystem,
        #[Config('gfmodules.nvi.list_code_display')]
        protected string $listCodeDisplay,
    ) {
    }

    /**
     * @throws GuzzleException
     */
    private function getOauthToken(string $scope): string
    {
        $response = $this->oauthClient->post('oauth/token', [
            'form_params' => [
                'target_audience' => $this->nviUrl,
                'grant_type' => 'client_credentials',
                'scope' => $scope,
            ],
        ]);

        $data = json_decode((string) $response->getBody(), true);

        return $data['access_token'];
    }

    /**
     * @throws GuzzleException
     */
    public function retrieveList(string $subjectIdentifier): array
    {
        $token = $this->getOauthToken(self::OAUTH_SCOPE_READ);

        $response = $this->nviClient->get('List', [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
            'query' => [
                'subject:identifier' => $this->subjectIdentifierSystem . '|' . $subjectIdentifier,
                'code' => 'LaboratoryTestResult',
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * @throws GuzzleException
     */
    public function createListReference(string $subjectIdentifier): void
    {
        $token = $this->getOauthToken(self::OAUTH_SCOPE_WRITE);

        $this->nviClient->post('List', [
            'headers' => [
                'Authorization' => "Bearer $token",
                'Content-Type' => 'application/fhir+json',
            ],
            'json' => [
                'resourceType' => 'List',
                'extension' => [[
                    'valueReference' => [
                        'identifier' => [
                            'system' => $this->custodianIdentifierSystem,
                            'value' => $this->custodianIdentifierValue,
                        ],
                    ],
                    'url' => $this->custodianExtensionUrl,
                ]],
                'subject' => [
                    'identifier' => [
                        'system' => $this->subjectIdentifierSystem,
                        'value' => $subjectIdentifier,
                    ],
                ],
                'source' => [
                    'identifier' => [
                        'system' => $this->sourceIdentifierSystem,
                        'value' => $this->sourceIdentifierValue,
                    ],
                    'type' => 'Device',
                ],
                'status' => 'current',
                'mode' => 'working',
                'emptyReason' => [
                    'coding' => [[
                        'code' => 'withheld',
                        'system' => 'http://terminology.hl7.org/CodeSystem/list-empty-reason',
                    ]],
                ],
                'code' => [
                    'coding' => [[
                        'code' => $this->listCode,
                        'system' => $this->listCodeSystem,
                        'display' => $this->listCodeDisplay,
                    ]],
                ],
            ],
        ]);
    }
}
