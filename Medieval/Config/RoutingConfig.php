<?php

namespace Medieval\Config;

class RoutingConfig {

    private static $_customMappings = array();

    const ROUTING_TYPE = 'custom';

    const MAX_REQUEST_PARAMS = 10;

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

        // Custom route with params
        self::$_customMappings[ 'test/login' ] = [ 'uri' => 'test/users/login', 'params' => [ 'mixed', 'mixed' ] ];

        // Custom route without params
        self::$_customMappings[ 'test/logout' ] = [ 'uri' => 'test/users/logout' ];

        return self::$_customMappings;
    }
}