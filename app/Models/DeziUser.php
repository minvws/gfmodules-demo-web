<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\DeziLoginException;
use App\Services\Dezi\DeziDeclarationService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class DeziUser implements Authenticatable
{
    public function __construct(
        public DeziDeclaration $declaration,
        public string $loaAuthn,
        public string $jwt
    ) {
    }

    /**
     * @param object{
     *     jti: string,
     *     iat: int,
     *     exp: int,
     *     iss: string,
     *     aud: string,
     *     loa_authn: string,
     *     json_schema: string,
     *     verklaring_id: string,
     *     verklaring: string
     * } $oidcResponse
     *
     * @throws DeziLoginException
     */
    public static function deserializeFromObject(object $oidcResponse): DeziUser
    {
        $requiredEnvelopeKeys = ['verklaring', 'verklaring_id', 'loa_authn'];
        $missingKeys = [];
        foreach ($requiredEnvelopeKeys as $key) {
            if (! property_exists($oidcResponse, $key)) {
                $missingKeys[] = $key;
            }
        }
        if (! empty($missingKeys)) {
            Log::error('DEZI envelope has missing required fields: ' . implode(', ', $missingKeys));
            throw new DeziLoginException("Invalid DEZI response received from identity provider.");
        }
        $rawDeclaration = $oidcResponse->verklaring;
        $declarationId = $oidcResponse->verklaring_id;
        $loaAuthn = $oidcResponse->loa_authn;

        $declarationService = new DeziDeclarationService();
        try {
            $declaration = $declarationService->createDeclaration(
                $rawDeclaration,
                $declarationId,
            );
        } catch (\Throwable $e) {
            Log::error('Error deserializing DeziUser: ' . $e->getMessage());
            throw new DeziLoginException("Invalid DEZI response received from identity provider.");
        }

        return new DeziUser(
            $declaration,
            $loaAuthn,
            $rawDeclaration,
        );
    }

    /**
     * Get the JWT token for the user.
     */
    public function getJwt(): ?string
    {
        return $this->jwt;
    }

    /**
     * Set the JWT token for the user.
     */
    public function setJwt(string $jwt): void
    {
        $this->jwt = $jwt;
    }

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getName(): string
    {
        return $this->declaration->initials . ' ' .
            $this->declaration->surnamePrefix . ' ' . $this->declaration->surname;
    }

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName(): string
    {
        return $this->declaration->deziNumber;
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier(): string
    {
        return $this->declaration->deziNumber;
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword(): string
    {
        throw new RuntimeException("Dezi uses can't have a password");
    }

    /**
     * Get the name of the password attribute for the user.
     */
    public function getAuthPasswordName(): string
    {
        throw new RuntimeException('No password for Dezi users');
    }

    /**
     * Get the token value for the "remember me" session.
     */
    public function getRememberToken(): string
    {
        throw new RuntimeException("Do not remember cookie's");
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     */
    public function setRememberToken($value): void
    {
        throw new RuntimeException("Do not remember cookie's");
    }

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName(): string
    {
        throw new RuntimeException("Do not remember cookie's");
    }

    public function getDisplayName(): string
    {
        if (empty($this->declaration->initials) || empty($this->declaration->surname)) {
            return $this->declaration->deziNumber;
        }

        return $this->declaration->initials
            . ($this->declaration->surnamePrefix ? ' ' . $this->declaration->surnamePrefix : '')
            . ' ' . $this->declaration->surname;
    }
}
