<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file contains security-related settings for the
    | TPS Dashboard application, following ISO 27001:2013 standards.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy
    |--------------------------------------------------------------------------
    */
    'csp' => [
        'enabled' => env('CONTENT_SECURITY_POLICY', true),
        'policy' => [
            'default-src' => "'self'",
            'script-src' => "'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
            'style-src' => "'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com",
            'font-src' => "'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com",
            'img-src' => "'self' data: https: http:",
            'connect-src' => "'self'",
            'frame-src' => "'none'",
            'object-src' => "'none'",
            'base-uri' => "'self'",
            'form-action' => "'self'",
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    */
    'headers' => [
        'enabled' => env('SECURE_HEADERS', true),
        'x-frame-options' => 'DENY',
        'x-content-type-options' => 'nosniff',
        'x-xss-protection' => '1; mode=block',
        'referrer-policy' => 'strict-origin-when-cross-origin',
        'permissions-policy' => 'geolocation=(), microphone=(), camera=()',
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Security
    |--------------------------------------------------------------------------
    */
    'upload' => [
        'max_size' => env('MAX_FILE_SIZE', 10240), // KB (10MB)
        'allowed_types' => explode(',', env('ALLOWED_FILE_TYPES', 'xlsx,xls')),
        'scan_uploads' => env('SCAN_UPLOADS', true),
        'quarantine_suspicious' => env('QUARANTINE_SUSPICIOUS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limiting' => [
        'api_requests' => env('RATE_LIMIT_REQUESTS', 60),
        'api_minutes' => env('RATE_LIMIT_MINUTES', 1),
        'login_attempts' => 5,
        'login_lockout' => 15, // minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Policy
    |--------------------------------------------------------------------------
    */
    'password_policy' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_symbols' => false,
        'password_history' => 5,
        'max_age_days' => 90,
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Security
    |--------------------------------------------------------------------------
    */
    'session_security' => [
        'timeout_minutes' => 60,
        'regenerate_on_login' => true,
        'destroy_on_logout' => true,
        'check_ip_changes' => false, // Set to true for high security
        'check_user_agent' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Logging
    |--------------------------------------------------------------------------
    */
    'audit' => [
        'enabled' => true,
        'log_login_attempts' => true,
        'log_data_access' => true,
        'log_data_modifications' => true,
        'log_file_operations' => true,
        'log_admin_actions' => true,
        'retention_days' => 365,
    ],

    /*
    |--------------------------------------------------------------------------
    | Export Security
    |--------------------------------------------------------------------------
    */
    'export' => [
        'password_protected' => true,
        'default_password' => env('EXPORT_PASSWORD', 'TPS123'),
        'log_exports' => true,
        'rate_limit_exports' => true,
    ],
];
