<?php

namespace Medieval\Config;

class RoutingConfig {

    private static $_customMappings = array();

    const ROUTING_TYPE = 'custom';

    const UNAUTHORIZED_REDIRECT = 'user/login';
    const AUTHORIZED_REDIRECT = 'main/home/welcome';

    public static function getCustomMappings() {

        // Example custom route
//        self::$_customMappings[] = [
//            'route' => [
//                'uri' => 'welcome',
//                'uriParams' => [ ],
//                'bindingParams' => [ ]
//            ],
//            'method' => 'GET',
//            'authorize' => false,
//            'admin' => false,
//            'defaultRoute' => 'main/home/welcome'
//        ];

        return self::$_customMappings;
    }
}