<?php

return [
    'connections' => [
        'default' => [
            'client_id' => env('BC_CLIENT_ID'),
            'client_secret' => env('BC_CLIENT_SECRET'),
            'tenant_id' => env('BC_TENANT_ID'),
            'environment' => env('BC_ENVIRONMENT', 'local'), // or production
            'company_id' => env('BC_COMPANY_ID'),
        ]
    ],
    'api_url' => env('BC_API_URL', 'https://api.businesscentral.dynamics.com/v2.0/'),
];
