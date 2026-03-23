<?php

return [
    'name' => 'Notifications',

    'queue' => env('NOTIFICATIONS_QUEUE', 'default'),

    'channels' => [
        'mail' => [
            'provider' => env('MAIL_MAILER', 'smtp'),
        ],
        'webpush' => [
            'provider' => env('NOTIFICATIONS_WEBPUSH_PROVIDER', 'webpush'),
        ],
        'sms' => [
            'provider' => env('NOTIFICATIONS_SMS_PROVIDER', 'twilio'),
        ],
    ],

    'circuit_breaker' => [
        'open_minutes' => (int) env('NOTIFICATIONS_CIRCUIT_OPEN_MINUTES', 15),
        'failure_threshold' => (int) env('NOTIFICATIONS_CIRCUIT_FAILURE_THRESHOLD', 3),
    ],

    'notification_types' => [
        'worship_roster',
        'ebd_lesson',
        'churchcouncil_minutes',
        'sermon_collaboration',
        'treasury_approval',
        'event_registration',
        'payment_completed',
        'student_leveled_up',
        'generic',
    ],
];
