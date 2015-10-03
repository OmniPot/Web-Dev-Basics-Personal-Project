<?php

namespace Medieval\Config;

class RoutingConfig {

    private static $_customMappings = array();

    const ROUTING_TYPE = 'custom';

    const UNAUTHORIZED_REDIRECT = 'user/login';
    const AUTHORIZED_REDIRECT = 'home/welcome';

    /**
     * This method returns custom defined routes that map to existing ones.
     * 'uri' key keeps the existing route
     * 'params' keeps the needed uri params to call the action.
     * See below for example.
     * @return array
     */
    public static function getCustomMappings() {

        // Example custom route
        self::$_customMappings[] = [
            'route' => [
                'uri' => 'welcome',
                'params' => [ ]
            ],
            'method' => 'GET',
            'authorize' => false,
            'admin' => false,
            'defaultRoute' => 'main/home/welcome'
        ];

        return self::$_customMappings;
    }
}