<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use MinVWS\OpenIDConnectLaravel\Http\Responses\LoginResponseHandlerInterface;
use Symfony\Component\HttpFoundation\Response;
use OpenSSLAsymmetricKey;

class DigidMockController extends Controller
{
    private readonly OpenSSLAsymmetricKey $privateKey;
    private readonly array $x5c;

    public function __construct(
        private readonly LoginResponseHandlerInterface $loginResponseHandler
    ) {
        // Load RSA private key and certificate from config once
        $certificate = file_get_contents(config('auth.uzi_jwt_signing_cert'));
        if (!$certificate) {
            throw new \RuntimeException('UZI JWT signing certificate not configured');
        }
        $privateKey = file_get_contents(config('auth.uzi_jwt_signing_key'));
        if (!$privateKey) {
            throw new \RuntimeException('UZI JWT signing key not configured');
        }
        $this->privateKey = openssl_pkey_get_private($privateKey)
             ?: throw new \RuntimeException('UZI JWT signing key not configured');


        $this->x5c = [base64_encode($this->convertCertToDer($certificate))];
    }

    private function createMockJwt(): string
    {
        $payload = [
            "aud" => "",
            "exp" => time() + 3600, // 1 hour from now
            "initials" => "m",
            "iss" => "",
            "loa_authn" => "",
            "loa_uzi" => "Basis",
            "nbf" => time(),
            "relations" => [
                [
                    "entity_name" => "BossEntity",
                    "roles" => [
                        "01.000"
                    ],
                    "ura" => "12341234"
                ]
            ],
            "sub" => "",
            "surname" => "Boss",
            "surname_prefix" => "",
            "uzi_id" => "12312123"
        ];

        // Create custom header with x5c (Firebase JWT doesn't support array values in headers)
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT',
            'x5c' => $this->x5c
        ];

        // Manually create JWT since Firebase JWT doesn't support x5c array
        $headerEncoded = $this->base64UrlEncode(json_encode($header)
            ?: throw new \RuntimeException('Failed to encode header'));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload)
            ?: throw new \RuntimeException('Failed to encode payload'));

        $signatureData = $headerEncoded . '.' . $payloadEncoded;
        $signature = '';

        if (!openssl_sign($signatureData, $signature, $this->privateKey, OPENSSL_ALGO_SHA256)) {
            throw new \RuntimeException('Failed to sign JWT with RSA key');
        }

        $signatureEncoded = $this->base64UrlEncode($signature);

        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function convertCertToDer(string $certificate): string
    {
        // Remove PEM headers and decode to get DER format
        $cert = str_replace(['-----BEGIN CERTIFICATE-----', '-----END CERTIFICATE-----', "\n", "\r"], '', $certificate);
        return base64_decode($cert) ?: throw new \RuntimeException('Failed to decode certificate');
    }


    public function login(): Response
    {
        // A mock JWT is placed in session when DIGID mock is enabled
        // If normal flow with Dezi login is enabled, we don't need to do anything special:
        //      setting JWT in session is then handled by JweDecryptService
        Session::put('oidc_jwt', $this->createMockJwt());
        return $this->loginResponseHandler->handleLoginResponse(
            (object)[
                "relations" => [(object)['entity_name' => "BossEntity", 'ura' => "12341234", 'roles' => ["01.000"]]],
                "initials" => "m",
                "surname" => "Boss",
                "surname_prefix" => "",
                "uzi_id" => "12312123",
                "loa_uzi" => "Basis",
            ]
        );
    }
}
