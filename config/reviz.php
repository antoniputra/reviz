<?php

return [

    /**
     * Globally Ignore any fields from logging
     * 
     * @var array
     */
    'ignore_fields' => [
        'updated_at',
        // ...
    ],

    /**
     * You might optionally defines your models
     * custom morph map with Reviz table
     * 
     * @var array
     */
    'morphMap' => [
        // 'users' => App\User::class,
    ],

    /**
     * UI Settings
     */
    'ui' => [

        /**
         * UI Status
         */
        'enabled' => env('REVIZ_UI_ENABLED', true),

        /**
         * Get user email field
         * @var string
         */
        'user_email' => 'email',

        /**
         * Get user name field
         * @var string
         */
        'user_name' => 'name',

        /**
         * UI Middleware
         */
        'middleware' => ['web'],

        /**
         * Authorized email to access this UI
         * @var array
         */
        'authorized_emails' => [
            // 'admin@example.com'
        ],
    
        /**
         * Reviz Path
         */
        'prefixPath' => env('REVIZ_UI_PREFIX_PATH', 'reviz-panel'),
    ],

];
