<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;

class DemoService
{
    /**
     * The paths to the mTLS certificate and key. These are used to authenticate with the PRS and NVI.
     * @var string
     */
    protected string $mtls_cert;
    /***
     * The paths to the mTLS certificate and key. These are used to authenticate with the PRS and NVI.
     * @var string
     */
    protected string $mtls_key;

    /**
     * The verify option for Guzzle. This can be set to false to disable SSL verification, or it can
     * be set to a path to a CA bundle.
     *
     * @var string|bool
     */
    protected string|bool $verify;

    public function __construct(string $mtls_cert, string $mtls_key, string|bool $verify)
    {
        $this->mtls_cert = $mtls_cert;
        $this->mtls_key = $mtls_key;
        $this->verify = $verify;
    }

    /**
     * Retrieves an OAuth token for the given URL (audience)
     * @param string $url
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getOauthToken(string $url): string
    {
        $client = new Client([
            'base_uri' => config('gfmodules.oauth.url'),
            'cert' => $this->mtls_cert,
            'ssl_key' => $this->mtls_key,
            'verify' => $this->verify,
        ]);

        $response = $client->post('/oauth/token', [
            'form_params' => [
                'target_audience' => $url,
                'grant_type' => 'client_credentials',
                'scope' => 'epd:read',
            ],
        ]);

        $data = json_decode((string) $response->getBody(), true);
        return $data['access_token'];
    }

    /**
     * This creates a blinded input for a given hashed BSN. We do this by calling a test endpoint on the PRS, since PHP
     * does not have the necessary cryptographic libraries to do this locally.
     *
     * @param string $token
     * @param string $hashed_bsn
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createPrsInput(string $token, string $hashed_bsn): array
    {
        $client = new Client([
            'base_uri' => config('gfmodules.prs.url'),
            'cert' => $this->mtls_cert,
            'ssl_key' => $this->mtls_key,
            'verify' => $this->verify,
        ]);

        $response = $client->post('/test/oprf/client', [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
            'json' => [
                'personalId' => "NL:bsn:$hashed_bsn",
            ],
        ]);

        return json_decode((string)$response->getBody(), true);
    }

    /**
     * Evaluate the blinded input with the PRS and return the result.
     *
     * @param string $token
     * @param string $input
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function prsEvaluate(string $token, string $input): array
    {
        $client = new Client([
            'base_uri' => config('gfmodules.prs.url'),
            'cert' => $this->mtls_cert,
            'ssl_key' => $this->mtls_key,
            'verify' => $this->verify,
        ]);

        $response = $client->post('/oprf/eval', [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
            'json' => [
                'encryptedPersonalId' => $input,
                'recipientOrganization' => 'ura:90000201',
                'recipientScope' => 'nationale-verwijsindex',
            ],
        ]);

        return json_decode((string)$response->getBody(), true);
    }

    /**
     * Retrieves data from the NVI for a given token, eval_input and blind_factor.
     *
     * @param string $token
     * @param string $eval_input
     * @param string $blind_factor
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function retrieveFromNVI(string $token, string $eval_input, string $blind_factor): array
    {
        $client = new Client([
            'base_uri' => config('gfmodules.nvi.url'),
            'cert' => $this->mtls_cert,
            'ssl_key' => $this->mtls_key,
            'verify' => $this->verify,
        ]);

        $response = $client->post('/Organization/$localize', [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
            'json' => [
                'resourceType' => 'Parameters',
                'parameter' => [
                    [
                        'name' => 'pseudonym',
                        'valueString' => $eval_input,
                    ],
                    [
                        'name' => 'oprfKey',
                        'valueString' => $blind_factor,
                    ],
                    [
                        'name' => 'careContext',
                        'valueCoding' => [
                            'system' => 'http://nictiz.nl/fhir/hcim-2024',
                            'code' => 'LaboratoryTestResult',
                        ],
                    ],
                ]
            ],
        ]);

        return json_decode((string)$response->getBody(), true);
    }


    /**
     * Creates a NVIDataReference for the given BSN. This is used to demonstrate the full flow of creating a reference
     * in the NVI and then retrieving it.
     *
     * @param string $bsn
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createNVIDataReference(string $bsn): void
    {
        $hashed_bsn = hash_hmac('sha256', $bsn, config('gfmodules.hmac.key'));
        $token = $this->getOauthToken(config('gfmodules.prs.url'));

        $data = $this->createPrsInput($token, $hashed_bsn);
        $blind_factor = $data['blind_factor'];

        $result = $this->prsEvaluate($token, $data['blinded_input']);
        $eval_input = $result['jwe'];

        $token = $this->getOauthToken(config('gfmodules.nvi.url'));
        $client = new Client([
            'base_uri' => config('gfmodules.nvi.url'),
            'cert' => $this->mtls_cert,
            'ssl_key' => $this->mtls_key,
            'verify' => $this->verify,
        ]);

        $client->post('/NVIDataReference', [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
            'json' => [
                'resourceType' => 'NVIDataReference',
                'source' => [
                    'system' => "urn:oid:2.16.528.1.1007.3.3",
                    'value' => "90000101"
                ],
                'sourceType' => [
                    'coding' => [
                        [
                            'system' => 'http://vws.nl/fhir/CodeSystem/nvi-organization-types',
                            'code' => 'laboratorium',
                        ],
                    ],
                ],
                'careContext' => [
                    'coding' => [
                        [
                            'system' => 'http://nictiz.nl/fhir/hcim-2024',
                            'code' => 'LaboratoryTestResult',
                        ],
                    ],
                ],
                'subject' => [
                    'system' => 'http://vws.nl/fhir/NamingSystem/nvi-pseudonym',
                    'value' => $eval_input,
                ],
                'oprfKey' => $blind_factor,

            ],
        ]);
    }
}
