<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\CompactSerializer;
use MinVWS\OpenIDConnectLaravel\Http\Responses\LoginResponseHandlerInterface;
use Symfony\Component\HttpFoundation\Response;
use Jose\Component\Core\JWK;

class DigidMockController extends Controller
{
    private readonly JWK $signingKey;
    private readonly JWK $publicKey;
    private readonly JWSBuilder $jwsBuilder;
    private readonly CompactSerializer $serializer;
    private readonly string $kid;

    public function __construct(
        private readonly LoginResponseHandlerInterface $loginResponseHandler
    ) {
        // Load private key for signing
        $privateKeyPath = config('auth.uzi_jwt_signing_key');
        if (!$privateKeyPath || !file_exists($privateKeyPath)) {
            throw new \RuntimeException('UZI JWT signing key not configured or not found');
        }
        $this->signingKey = JWKFactory::createFromKeyFile($privateKeyPath);

        // Load certificate for x5c/x5t headers
        $certificatePath = config('auth.uzi_jwt_signing_cert');
        if (!$certificatePath || !file_exists($certificatePath)) {
            throw new \RuntimeException('UZI JWT signing certificate not configured or not found');
        }
        $this->publicKey = JWKFactory::createFromCertificateFile($certificatePath);
        $this->kid = $this->kidFromCertificate($certificatePath);

        // Initialize Jose components
        $algorithmManager = new AlgorithmManager([new RS256()]);
        $this->jwsBuilder = new JWSBuilder($algorithmManager);
        $this->serializer = new CompactSerializer();
    }

    /**
     * Generate a "kid" (Key ID) from a certificate.
     * The "kid" is a unique identifier for the key. There is no standard way to generate a kid.
     */
    private function kidFromCertificate(string $certificatePath): string
    {
        $certificateContent = file_get_contents($certificatePath);
        if ($certificateContent === false) {
            throw new \RuntimeException('Unable to read certificate file');
        }

        // Parse the certificate
        $certificate = openssl_x509_read($certificateContent);
        if ($certificate === false) {
            throw new \RuntimeException('Unable to parse certificate');
        }

        // Get the certificate fingerprint (SHA256)
        $fingerprint = openssl_x509_fingerprint($certificate, 'sha256', true);
        if ($fingerprint === false) {
            throw new \RuntimeException('Unable to generate certificate fingerprint');
        }

        // Convert to URL-safe base64
        return strtr(base64_encode($fingerprint), '+/', '-_');
    }

    private function createMockPayload(): array
    {
        return [
            "aud" => "",
            "exp" => time() + 3600,
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
    }

    private function createMockJws(): string
    {
        $jws = $this->jwsBuilder
            ->create()
            ->withPayload(json_encode($this->createMockPayload()) ?: '')
            ->addSignature($this->signingKey, [
                'alg' => 'RS256',
                'typ' => 'JWT',
                'x5t' => rtrim($this->publicKey->get('x5t'), '='), // Remove padding
                'kid' => $this->kid,
            ])
            ->build();

        return $this->serializer->serialize($jws, 0);
    }

    public function login(): Response
    {
        // A mock JWT is placed in session when DIGID mock is enabled
        // If normal flow with Dezi login is enabled, we don't need to do anything special:
        //      setting JWT in session is then handled by JweDecryptService
        Session::put('oidc_jwt', $this->createMockJws());
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
