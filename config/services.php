<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],


    // La configuration PayDunya vit dans son propre fichier : config/paydunya.php

    'fraude' => [
        'velocite_max_tentatives' => env('FRAUDE_VELOCITE_MAX_TENTATIVES', 5),
        'velocite_fenetre_minutes' => env('FRAUDE_VELOCITE_FENETRE_MINUTES', 10),
        'alerte_critique_mail_immediat' => env('FRAUDE_ALERTE_CRITIQUE_MAIL_IMMEDIAT', true),
    ],
];
