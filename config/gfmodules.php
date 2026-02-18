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
    ],
];
