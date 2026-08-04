<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Container\Attributes\Config;
use Illuminate\Container\Attributes\Give;
use JsonException;
use Noxlogic\Oprf\OprfClient;

class PrsService
{
    protected const OAUTH_SCOPE_READ = 'prs:read';

    public function __construct(
        #[Give('gfmodules.prs_client')]
        protected Client $prsClient,
        #[Give('gfmodules.prs_oauth_client')]
        protected Client $oauthClient,
        protected OAuthTokenService $oauthTokenService,
        protected OprfClient $oprfClient,
        #[Config('gfmodules.prs.url')]
        protected string $prsUrl,
        #[Config('gfmodules.prs.client_organization_id')]
        protected string $clientOrganizationId,
        #[Config('gfmodules.prs.recipient_organization')]
        protected string $recipientOrganization,
        #[Config('gfmodules.prs.recipient_scope')]
        protected string $recipientScope,
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
            $this->prsUrl,
            $scope,
            [
                'organization_id' => $this->clientOrganizationId,
            ],
        );
    }

    public function createInput(string $bsn): array
    {
        $personalIdentifier = [
            'landCode' => 'NL',
            'type' => 'BSN',
            'value' => $bsn,
        ];

        $info = $this->recipientOrganization . '|' . $this->recipientScope . '|v1';
        $pid = json_encode($personalIdentifier, JSON_THROW_ON_ERROR);
        $pseudoInput = hash_hkdf('sha256', $pid, 32, $info, '');
        $blind = $this->oprfClient->blind($pseudoInput);

        return [
            'blind_factor' => sodium_bin2base64($blind->blind, SODIUM_BASE64_VARIANT_URLSAFE),
            'blinded_input' => sodium_bin2base64($blind->blindedElement, SODIUM_BASE64_VARIANT_URLSAFE),
        ];
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function evaluate(string $input): array
    {
        $token = $this->getOauthToken(self::OAUTH_SCOPE_READ);

        $response = $this->prsClient->post('oprf/eval', [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
            'json' => [
                'encryptedPersonalId' => $input,
                'recipientOrganization' => $this->recipientOrganization,
                'recipientScope' => $this->recipientScope,
            ],
        ]);

        return json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }
}
