<?php

declare(strict_types=1);

return [
    'hmac' => [
        'key' => env('HMAC_KEY'),
    ],
    'oauth' => [
        'url' => env('GF_OAUTH_URL'),
    ],
    'prs' => [
        'url' => env('GF_PRS_URL'),
    ],
    'nvi' => [
        'url' => env('GF_NVI_URL'),
        'custodian_identifier_value' => env('GF_NVI_CUSTODIAN_IDENTIFIER_VALUE', '90000206'),
    ],
    'client_cert' => env('GF_CLIENT_CERT'),
    'client_key' => env('GF_CLIENT_KEY'),
    'client_verify' => env('GF_CLIENT_VERIFY', true),
];
