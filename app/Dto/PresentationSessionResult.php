<?php

declare(strict_types=1);

namespace App\Dto;

use InvalidArgumentException;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use JsonException;

class PresentationSessionResult
{
    public function __construct(
        protected string $id,
        protected string $vpToken,
        protected array $rawTokenResponse = [],
        protected bool $verificationResult = false,
        protected array $policyResults = [],
        protected array $providedCredentials = [],
    ) {
    }

    /**
     * @throws JsonException
     */
    public static function parse(array $data): self
    {
        $id = $data['id'] ?? null;
        if (!is_string($id) || empty($id)) {
            throw new InvalidArgumentException('Invalid or missing "id" in session of verifier response.');
        }

        $vpToken = $data['tokenResponse']['vp_token'] ?? null;
        if (!is_string($vpToken) || empty($vpToken)) {
            throw new InvalidArgumentException('Invalid or missing "vp_token" in tokenResponse of verifier response.');
        }

        // Only use the credential if the verification result is true.
        $verificationResult = $data['verificationResult'] ?? false;
        if ($verificationResult !== true) {
            throw new InvalidArgumentException('Session is not validat.');
        }

        $credentials = self::parseProvidedCredentials($vpToken);

        return new self(
            id: $id,
            vpToken: $vpToken,
            rawTokenResponse: $data['tokenResponse'] ?? [],
            verificationResult: $verificationResult,
            policyResults: $data['policyResults'] ?? [],
            providedCredentials: $credentials
        );
    }

    /**
     * @throws JsonException
     */
    public function getFirstCredentialSubject(): array
    {
        if (empty($this->providedCredentials)) {
            return [];
        }

        $firstCredential = $this->providedCredentials[0];
        $payload = self::getPayloadFromJwsString($firstCredential);

        return $payload['vc']['credentialSubject'] ?? [];
    }

    /**
     * Returns the raw credentials from the VP token.
     * Warning: This method assumes that the VP token is valid and that the credentials are validated by the verifier.
     *
     * @param string $vpToken
     * @return string[] Array of raw credentials in JWT format
     * @throws JsonException
     */
    protected static function parseProvidedCredentials(string $vpToken): array
    {
        $payload = self::getPayloadFromJwsString($vpToken);

        return $payload['vp']['verifiableCredential'] ?? [];
    }

    protected static function getPayloadFromJwsString(string $input): array
    {
        $serializerManager = new JWSSerializerManager([
            new CompactSerializer(),
        ]);

        // We can also split the token by '.' and decode the payload, but for now we use the serializer.
        $jws = $serializerManager->unserialize($input);

        $payload = $jws->getPayload();
        if ($payload === null) {
            throw new InvalidArgumentException('Invalid JWS: No payload found.');
        }

        return json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
    }
}
