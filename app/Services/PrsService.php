<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Container\Attributes\Config;
use Illuminate\Container\Attributes\Give;
use Noxlogic\Oprf\OprfClient;

class PrsService
{
    protected const OAUTH_SCOPE_READ = 'prs:read';

    protected const AUTHORIZED_ROLE_CONSULTING = 'consulting';

    public function __construct(
        #[Give('gfmodules.prs_client')]
        protected Client $prsClient,
        #[Give('gfmodules.prs_oauth_client')]
        protected Client $oauthClient,
        protected OprfClient $oprfClient,
        #[Config('gfmodules.prs.url')]
        protected string $prsUrl,
        #[Config('gfmodules.prs.recipient_organization')]
        protected string $recipientOrganization,
        #[Config('gfmodules.prs.recipient_scope')]
        protected string $recipientScope,
    ) {
    }

    /**
     * @throws GuzzleException
     */
    private function getOauthToken(string $scope, string $authorizedRole): string
    {
        $response = $this->oauthClient->post('token', [
            'form_params' => [
                'target_audience' => rtrim($this->prsUrl, '/'),
                'grant_type' => 'client_credentials',
                'scope' => $scope,
                'authorized_role' => $authorizedRole, // TODO: Check if needed
//                'source_id' => 'some-source-id', // TODO: Check if needed
                'org_oin' => '<replace-with-oin>', // TODO: Configure based on config or extract from certificate
            ],
        ]);

        $data = json_decode((string) $response->getBody(), true);

        return $data['access_token'];
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
     */
    public function evaluate(string $input): array
    {
        $token = $this->getOauthToken(self::OAUTH_SCOPE_READ, self::AUTHORIZED_ROLE_CONSULTING);

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

        return json_decode((string) $response->getBody(), true);
    }
}
