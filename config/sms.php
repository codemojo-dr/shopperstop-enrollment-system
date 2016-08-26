<?php

return [
    'provider' => 'dial2verify',
    'test' => env('SMS_TEST', false),
    'sender' => env('SMS_SENDER_ID'),
    'plivo' => [
        'id' => env('PLIVO_AUTH_ID'),
        'token' => env('PLIVO_AUTH_TOKEN')
    ],
    'dial2verify' => [
        'key' => env('DIAL2VERIFY_KEY')
    ]
];