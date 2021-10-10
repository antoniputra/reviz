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
         * Reviz Domain
         */
        'domain' => env('REVIZ_UI_DOMAIN', null),
    
        /**
         * Reviz Path
         */
        'path' => env('REVIZ_UI_PATH', 'reviz-panel'),

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
    ],

];
