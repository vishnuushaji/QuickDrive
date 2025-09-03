<?php

return [
    'paths' => ['api/*'],
    
    'allowed_methods' => ['*'],
    
    'allowed_origins' => ['*'], // Allow all origins for API
    
    'allowed_origins_patterns' => [],
    
    'allowed_headers' => ['*'],
    
    'exposed_headers' => [],
    
    'max_age' => 0,
    
    'supports_credentials' => false, // No credentials needed for Bearer tokens
];