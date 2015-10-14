<?php

namespace Medieval\Config;

class RoutingConfig {

    private static $_customMappings = [ ];

    const ROUTING_TYPE = 'custom';

    const UNAUTHORIZED_REDIRECT = 'user/login';
    const AUTHORIZED_REDIRECT = 'main/home/welcome';

    public static function getCustomMappings() {

        // Example custom route
        self::$_customMappings[] = [
            'customRoute' => [
                'uri' => 'home',
                'uriParams' => [ ],
                'bindingParams' => [ ]
            ],
            'method' => 'GET',
            'authorize' => true,
            'admin' => false,
            'defaultRoute' => 'main/home/welcome'
        ];

        return self::$_customMappings;
    }
}