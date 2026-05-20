<?php

declare(strict_types=1);

return [
    'prs' => [
        'url' => env('GF_PRS_URL'),
        'oauth_url' => env('GF_PRS_OAUTH_URL'),
        'recipient_organization' => env('GF_PRS_RECIPIENT_ORGANIZATION'),
        'recipient_scope' => env('GF_PRS_RECIPIENT_SCOPE'),
    ],
    'nvi' => [
        'url' => env('GF_NVI_URL'),
        'oauth_url' => env('GF_NVI_OAUTH_URL'),
        'subject_identifier_system' => env(
            'GF_NVI_SUBJECT_IDENTIFIER_SYSTEM',
            'http://minvws.github.io/generiekefuncties-docs/NamingSystem/nvi-identifier'
        ),
        'custodian_extension_url' => env(
            'GF_NVI_CUSTODIAN_EXTENSION_URL',
            'http://minvws.github.io/generiekefuncties-docs/StructureDefinition/nl-gf-localization-custodian'
        ),
        'custodian_identifier_system' => env(
            'GF_NVI_CUSTODIAN_IDENTIFIER_SYSTEM',
            'http://fhir.nl/fhir/NamingSystem/ura'
        ),
        'custodian_identifier_value' => env('GF_NVI_CUSTODIAN_IDENTIFIER_VALUE'),
        'source_identifier_system' => env('GF_NVI_SOURCE_IDENTIFIER_SYSTEM', 'urn:ietf:rfc:3986'),
        'source_identifier_value' => env('GF_NVI_SOURCE_IDENTIFIER_VALUE'),
        'list_code' => env('GF_NVI_LIST_CODE', 'LaboratoryTestResult'),
        'list_code_system' => env(
            'GF_NVI_LIST_CODE_SYSTEM',
            'http://minvws.github.io/generiekefuncties-docs/CodeSystem/nl-gf-data-categories-cs'
        ),
        'list_code_display' => env('GF_NVI_LIST_CODE_DISPLAY', 'Laboratorium Uitslagen'),
    ],
    'client_cert' => env('GF_CLIENT_CERT'),
    'client_key' => env('GF_CLIENT_KEY'),
    'client_verify' => env('GF_CLIENT_VERIFY', true),
];
