<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | PRS
    |--------------------------------------------------------------------------
    */

    'prs' => [
        /**
         * Base URL for the PRS API.
         *
         * Keep a trailing slash (/) so relative endpoints resolve correctly.
         * See Guzzle request URL behavior:
         * https://github.com/guzzle/guzzle/blob/7.10/docs/quickstart.rst#making-a-request
         */
        'url' => env('GF_PRS_URL'),

        /**
         * OAuth base URL for PRS.
         *
         * Keep a trailing slash (/) so relative endpoints resolve correctly.
         * See Guzzle request URL behavior:
         * https://github.com/guzzle/guzzle/blob/7.10/docs/quickstart.rst#making-a-request
         */
        'oauth_url' => env('GF_PRS_OAUTH_URL'),

        /**
         * Recipient organization identifier sent with PRS requests (for example: ura:90000201).
         */
        'recipient_organization' => env('GF_PRS_RECIPIENT_ORGANIZATION'),

        /**
         * Recipient scope used for PRS authorization.
         */
        'recipient_scope' => env('GF_PRS_RECIPIENT_SCOPE', 'nationale-verwijsindex'),
    ],

    /*
    |--------------------------------------------------------------------------
    | NVI
    |--------------------------------------------------------------------------
    */

    'nvi' => [
        /**
         * Base URL for the NVI API.
         *
         * Keep a trailing slash (/) so relative endpoints resolve correctly.
         * See Guzzle request URL behavior:
         * https://github.com/guzzle/guzzle/blob/7.10/docs/quickstart.rst#making-a-request
         */
        'url' => env('GF_NVI_URL'),

        /**
         * OAuth base URL for NVI.
         *
         * Keep a trailing slash (/) so relative endpoints resolve correctly.
         * See Guzzle request URL behavior:
         * https://github.com/guzzle/guzzle/blob/7.10/docs/quickstart.rst#making-a-request
         */
        'oauth_url' => env('GF_NVI_OAUTH_URL'),

        /**
         * Identifier system used for patient / subject identifiers in NVI queries.
         */
        'subject_identifier_system' => env(
            'GF_NVI_SUBJECT_IDENTIFIER_SYSTEM',
            'http://minvws.github.io/generiekefuncties-docs/NamingSystem/nvi-identifier'
        ),

        /**
         * FHIR extension URL used to represent the custodian localization.
         */
        'custodian_extension_url' => env(
            'GF_NVI_CUSTODIAN_EXTENSION_URL',
            'http://minvws.github.io/generiekefuncties-docs/StructureDefinition/nl-gf-localization-custodian'
        ),

        /**
         * Identifier system used for custodian identifiers.
         */
        'custodian_identifier_system' => env(
            'GF_NVI_CUSTODIAN_IDENTIFIER_SYSTEM',
            'http://fhir.nl/fhir/NamingSystem/ura'
        ),

        /**
         * Identifier value for the configured custodian (organization URA number).
         */
        'custodian_identifier_value' => env('GF_NVI_CUSTODIAN_IDENTIFIER_VALUE'),

        /**
         * Identifier system used for the source organization / resource.
         */
        'source_identifier_system' => env('GF_NVI_SOURCE_IDENTIFIER_SYSTEM', 'urn:ietf:rfc:3986'),

        /**
         * Identifier value used for the source organization / resource.
         */
        'source_identifier_value' => env('GF_NVI_SOURCE_IDENTIFIER_VALUE', 'EHR-SYS-2024-001'),

        /**
         * Data category code used when requesting the list of records.
         */
        'list_code' => env('GF_NVI_LIST_CODE', 'LaboratoryTestResult'),

        /**
         * Code system for the configured data category code.
         */
        'list_code_system' => env(
            'GF_NVI_LIST_CODE_SYSTEM',
            'http://minvws.github.io/generiekefuncties-docs/CodeSystem/nl-gf-data-categories-cs'
        ),

        /**
         * Human-readable display label for the configured data category code.
         */
        'list_code_display' => env('GF_NVI_LIST_CODE_DISPLAY', 'Laboratorium Uitslagen'),
    ],

    /*
    |--------------------------------------------------------------------------
    | mTLS
    |--------------------------------------------------------------------------
    */

    /**
     * Path to the client certificate (mTLS), if required by the remote API.
     */
    'client_cert' => env('GF_CLIENT_CERT'),

    /**
     * Path to the private key belonging to the configured client certificate.
     */
    'client_key' => env('GF_CLIENT_KEY'),

    /**
     * TLS server certificate verification (true/false or CA bundle path).
     */
    'client_verify' => env('GF_CLIENT_VERIFY', true),
];
