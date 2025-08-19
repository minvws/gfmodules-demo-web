<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use MinVWS\OpenIDConnectLaravel\Http\Responses\LoginResponseHandlerInterface;
use Symfony\Component\HttpFoundation\Response;

class DigidMockController extends Controller
{
    public function __construct(
        private readonly LoginResponseHandlerInterface $loginResponseHandler
    ) {
    }

    public static function urlsafeB64Encode(string $input): string
    {
        return \str_replace('=', '', \strtr(\base64_encode($input), '+/', '-_'));
    }

    private function createMockJwt(): string
    {
        $header = [
            'alg' => 'HS256',
        ];

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

        $headerEncoded = self::urlsafeB64Encode(json_encode($header) ?: '');
        $payloadEncoded = self::urlsafeB64Encode(json_encode($payload) ?: '');

        $signatureRaw = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, 'mock_secret', true);
        $signature = self::urlsafeB64Encode($signatureRaw);

        return $headerEncoded . '.' . $payloadEncoded . '.' . $signature;
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
