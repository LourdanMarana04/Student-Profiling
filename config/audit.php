<?php

return [
    // Fields to redact from stored snapshots and UI display
    'redact' => [
        'password',
        'password_confirmation',
        'ssn',
        'social_security_number',
        'credit_card',
        'card_number',
        'cvv',
        'access_token',
        'remember_token',
        'email',
    ],

    // Replacement text for redacted values
    'redaction_placeholder' => '[REDACTED]',

    // Whether to record read/access events (middleware still needs registration)
    'record_reads' => true,
];
