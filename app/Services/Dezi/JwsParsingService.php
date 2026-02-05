<?php

declare(strict_types=1);

namespace App\Services\Dezi;

use App\Exceptions\DeziDeclarationException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Jose\Component\Checker\AlgorithmChecker;
use Jose\Component\Checker\HeaderCheckerManager;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWK;
use Jose\Component\Core\JWKSet;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSLoader;
use Jose\Component\Signature\JWSTokenSupport;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;

final class JwsParsingService
{
    private ?JWK $mockSignJwk = null;
    private JWSLoader $jwsLoader;
    private HeaderCheckerManager $headerCheckerManager;
    private JWSSerializerManager $serializerManager;

    public function __construct()
    {
        if (config('auth.dezi_mock_enabled')) {
            $certPath = config('auth.dezi_mock_jwt_signing_cert');
            if (is_null($certPath) || ! file_exists($certPath)) {
                throw new \RuntimeException("Dezi mock signing certificate not found at path: {$certPath}");
            }
            $contents = file_get_contents($certPath);
            if ($contents === false) {
                throw new \RuntimeException("Failed to read Dezi mock signing certificate at path: {$certPath}");
            }
            $this->mockSignJwk = JWKFactory::createFromCertificate(
                $contents,
            );
        }
        $this->setupJoseComponents();
    }

    private function setupJoseComponents(): void
    {
        $this->serializerManager = new JWSSerializerManager([
            new CompactSerializer(),
        ]);
        $signatureAlgorithmManager = new AlgorithmManager([new RS256()]);
        $jwsVerifier = new JWSVerifier($signatureAlgorithmManager);
        $this->headerCheckerManager = new HeaderCheckerManager(
            [new AlgorithmChecker(['RS256'])],
            [new JWSTokenSupport()]
        );

        $this->jwsLoader = new JWSLoader(
            $this->serializerManager,
            $jwsVerifier,
            $this->headerCheckerManager
        );
    }

    public function verifyJwsAndGetClaims(string $jws): array
    {
        $jwsType = $this->serializerManager->unserialize($jws);
        $this->headerCheckerManager->check($jwsType, 0, ['jku', 'kid', 'alg']);
        $header = $jwsType->getSignature(0)->getProtectedHeader();
        $jwksUrl = $header['jku'];
        if (config('auth.dezi_mock_enabled') && $this->mockSignJwk !== null) {
            $keySet = new JWKSet([$this->mockSignJwk]);
        } else {
            $keySet = $this->getJwkSet($jwksUrl);
        }

        try {
            $signatureIndex = null;

            $jwsType = $this->jwsLoader->loadAndVerifyWithKeySet(
                $jws,
                $keySet,
                $signatureIndex
            );
            $json = $jwsType->getPayload();
            if ($json === null) {
                throw new DeziDeclarationException('JWT payload is null');
            }
            $payload = json_decode($json, true);

            if (! is_array($payload)) {
                throw new DeziDeclarationException('Invalid JWT payload');
            }

            return $payload;
        } catch (\Throwable $e) {
            Log::error('JWT verification failed: ' . $e->getMessage());
            throw new DeziDeclarationException('Failed to verify JWT');
        }
    }

    private function getJwkSet(string $jwksUrl): JWKSet
    {
        try {
            $client = new Client();

            $response = $client->get($jwksUrl, [
                'verify' => config('oidc.tls_verify'),
            ]);

            $jwksData = json_decode($response->getBody()->getContents(), true);

            return JWKSet::createFromKeyData($jwksData);
        } catch (\Throwable $e) {
            Log::error('Failed to retrieve JWKS: ' . $e->getMessage());
            throw new DeziDeclarationException('Failed to retrieve JWKS');
        }
    }
}
