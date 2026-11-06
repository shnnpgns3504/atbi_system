<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Cors extends BaseConfig
{
    public array $default = [
        /**
         * Allow all origins (*) so your Vue app can fetch images from the backend.
         */
        'allowedOrigins' => ['*'],

        'allowedOriginsPatterns' => [],

        /**
         * Set to false since we aren't using cookies for this specific cross-origin request.
         */
        'supportsCredentials' => false,

        /**
         * Allow all headers.
         */
        'allowedHeaders' => ['*'],

        'exposedHeaders' => [],

        /**
         * Allow all methods (GET, POST, etc).
         */
        'allowedMethods' => ['*'],

        'maxAge' => 7200,
    ];
}