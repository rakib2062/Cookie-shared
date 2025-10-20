<?php
// config/tracker.php
return [
    // Trusted site origins that are allowed to call tracker endpoints
    'allowed_origins' => explode(',', env('TRACKER_ALLOWED_ORIGINS', 'http://127.0.0.1:8002,http://127.0.0.1:8003')),

    // Domain used for tracker cookie. Use your tracker domain or parent domain.
    'cookie_domain' => env('TRACKER_COOKIE_DOMAIN', 'http://127.0.0.1:8001'),

    // Cookie lifetime in minutes (1 year default)
    'cookie_minutes' => env('TRACKER_COOKIE_MINUTES', 525600),

    // Rate limit (requests per minute) for /track
    'rate_limit_per_min' => env('TRACKER_RATE_LIMIT_PER_MIN', 60),
];
