<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Container\Attributes\Config;
use Illuminate\Container\Attributes\Give;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class NviService
{
    protected const OAUTH_SCOPE_READ = 'nvi:read';

    protected const OAUTH_SCOPE_WRITE = 'nvi:create';

    protected const OAUTH_SCOPE_DELETE = 'nvi:delete';

    protected const OAUTH_SCOPE_LOCALIZE = 'nvi:localize';

    public function __construct(
        #[Give('gfmodules.nvi_client')]
        protected Client $nviClient,
        #[Give('gfmodules.nvi_oauth_client')]
        protected Client $oauthClient,
        protected OAuthTokenService $oauthTokenService,
        #[Config('gfmodules.nvi.url')]
        protected string $nviUrl,
        #[Config('gfmodules.nvi.client_organization_id')]
        protected string $clientOrganizationId,
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
     * @throws JsonException
     */
    private function getOauthToken(string $scope): string
    {
        return $this->oauthTokenService->getAccessToken(
            $this->oauthClient,
            $this->nviUrl,
            $scope,
            [
                'source_id' => $this->sourceIdentifierValue,
                'organization_id' => $this->clientOrganizationId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function retrieveList(string $subjectIdentifier): array
    {
        $token = $this->getOauthToken(self::OAUTH_SCOPE_LOCALIZE);

        $response = $this->nviClient->get('fhir/List', [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
            'query' => [
                'subject:identifier' => $this->subjectIdentifierSystem . '|' . $subjectIdentifier,
            ],
        ]);

        return json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function createListReference(string $subjectIdentifier): void
    {
        $token = $this->getOauthToken(self::OAUTH_SCOPE_WRITE);

        try {
            $this->nviClient->post('fhir/List', [
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
        } catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() === Response::HTTP_CONFLICT) {
                // A 409 conflict is expected and can be ignored.
                return;
            }

            throw $exception;
        }
    }
}
