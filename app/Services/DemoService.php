<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Exception\GuzzleException;

class DemoService
{
    public function __construct(
        protected PrsService $prsService,
        protected NviService $nviService,
    ) {
    }

    /**
     * Creates a blinded input for the provided BSN.
     */
    public function createPrsInput(string $bsn): array
    {
        return $this->prsService->createInput($bsn);
    }

    /**
     * Evaluate the blinded input with the PRS and return the result.
     *
     * @throws GuzzleException
     */
    public function prsEvaluate(string $input): array
    {
        return $this->prsService->evaluate($input);
    }

    /** @throws GuzzleException */
    public function retrieveFromNVI(string $eval_input, string $blind_factor): array
    {
        $subjectIdentifier = $this->encodeSubjectIdentifier($eval_input, $blind_factor);

        return $this->nviService->retrieveList($subjectIdentifier);
    }

    /**
     * Creates an NVI List entry for the given BSN.
     *
     * @throws GuzzleException
     */
    public function createNviListEntry(string $bsn): void
    {
        $data = $this->createPrsInput($bsn);
        $blind_factor = $data['blind_factor'];

        $result = $this->prsEvaluate($data['blinded_input']);
        $eval_input = $result['jwe'];
        $subjectIdentifier = $this->encodeSubjectIdentifier($eval_input, $blind_factor);

        $this->nviService->createListReference($subjectIdentifier);
    }

    protected function encodeSubjectIdentifier(string $oprfJwe, string $blindFactor): string
    {
        $payload = json_encode([
            'evaluated_output' => $oprfJwe,
            'blind_factor' => $blindFactor,
        ], JSON_THROW_ON_ERROR);

        return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
    }
}
