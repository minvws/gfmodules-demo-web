<?php

declare(strict_types=1);

namespace App\Services\Dezi;

use Jose\Component\Core\AlgorithmManagerFactory;
use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWS;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\JWSBuilderFactory;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JSONGeneralSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Ramsey\Uuid\Uuid;

class MockService
{
    private string $privateKey;
    private JWSBuilder $jwsBuilder;
    private JWK $jwk;
    private JWSSerializerManager $serializerManager;

    public function __construct()
    {
        // Load private key for signing
        $privateKeyPath = config('auth.dezi_mock_jwt_signing_key');
        if (! $privateKeyPath || ! file_exists($privateKeyPath)) {
            throw new \RuntimeException('Mock DEZI JWT signing key not configured or not found');
        }
        $privateKey = file_get_contents($privateKeyPath);
        if ($privateKey === false) {
            throw new \RuntimeException('Failed to read Mock DEZI JWT signing key');
        }
        $this->privateKey = $privateKey;
        $this->setupJoseComponents();
    }

    private function setupJoseComponents(): void
    {
        $algorithmManagerFactory = new AlgorithmManagerFactory([new RS256()]);
        $this->jwsBuilder = (new JWSBuilderFactory($algorithmManagerFactory))->create(['RS256']);
        $this->jwk = JWKFactory::createFromKey($this->privateKey);
        $this->serializerManager = new JWSSerializerManager([
            new CompactSerializer(),
            new JSONGeneralSerializer(),
        ]);
    }

    public function serializeJws(JWS $jws): string
    {
        return $this->serializerManager->serialize('jws_compact', $jws, 0);
    }

    public function createMockEnvelopeJws(): JWS
    {
        $declarationId = Uuid::uuid4();
        $declaration = $this->createMockDeclaration($declarationId->toString());
        $serialized = $this->serializerManager->serialize(
            'jws_compact',
            $declaration,
            0
        );
        $payload = [
            'jti' => uniqid('mocked_', true),
            'iat' => time(),
            'exp' => time() + 3600,
            'iss' => config('auth.dezi_mock_issuer'),
            'aud' => Uuid::uuid4()->toString(),
            'json_schema' => 'https://example.com/mock-schema',
            'loa_authn' => config('auth.minimum_loa', 'http://eidas.europa.eu/LoA/substantial'),
            'verklaring_id' => $declarationId->toString(),
            'verklaring' => $serialized,
        ];
        $encoded = json_encode($payload);
        if ($encoded === false) {
            throw new \RuntimeException('Failed to encode JWS payload');
        }

        return $this->jwsBuilder
            ->create()
            ->withPayload($encoded)
            ->addSignature(
                $this->jwk,
                [
                    'alg' => 'RS256',
                    'kid' => 'mock-key',
                    'typ' => 'JWT',
                ]
            )
            ->build();
    }

    private function createMockDeclaration(string $declarationId): JWS
    {
        $headers = [
            'alg' => 'RS256',
            'kid' => 'mock-dezi-key-id',
            'jku' => 'https://example.com/jwks.json',
            'typ' => 'JWT',
        ];
        $payload = $this->createMockDeclarationPayload($declarationId);
        // Since this is a mock, a certificate with the public key for signature
        // verification won't be available on a public JWKS endpoint from Dezi.
        // The private key used here needs to be fetched instead.
        $json = json_encode($payload);
        if ($json === false) {
            throw new \RuntimeException('Failed to encode mock declaration payload');
        }

        return $this->jwsBuilder
            ->create()
            ->withPayload($json)
            ->addSignature(
                $this->jwk,
                $headers
            )
            ->build();
    }

    private function createMockDeclarationPayload(string $declarationId): array
    {
        return [
            'jti' => uniqid('mocked_', true),
            'iss' => config('auth.dezi_mock_issuer'),
            'exp' => time() + 3600,
            'nbf' => time(),
            'json_schema' => 'https://example.com/mock-declaration-schema',
            'loa_dezi' => config('auth.minimum_loa', 'http://eidas.europa.eu/LoA/substantial'),
            'verklaring_id' => $declarationId,
            'dezi_nummer' => '12312123',
            'voorletters' => 'M.',
            'voorvoegsel' => '',
            'achternaam' => 'Boss',
            'abonnee_nummer' => '12341234',
            'abonnee_naam' => 'BossEntity',
            'rol_code' => '01.000',
            'rol_naam' => 'Beheerder',
            'rol_code_bron' => 'https://example.com/role-codes',
        ];
    }
}
