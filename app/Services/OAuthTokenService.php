<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;

class OAuthTokenService
{
    /**
     * @param  array<string, string>  $additionalFormParameters
     *
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getAccessToken(
        Client $oauthClient,
        string $targetAudience,
        string $scope,
        array $additionalFormParameters = [],
    ): string {
        $response = $oauthClient->post('token', [
            'form_params' => [
                'target_audience' => rtrim($targetAudience, '/'),
                'grant_type' => 'client_credentials',
                'scope' => $scope,
            ] + $additionalFormParameters,
        ]);

        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return $data['access_token'];
    }
}
