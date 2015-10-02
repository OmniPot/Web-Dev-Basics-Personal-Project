<?php

namespace Medieval\Config;

class RoutingConfig {

    private static $_customMappings = array();

    const ROUTING_TYPE = 'custom';

    const UNAUTHORIZED_REDIRECT = 'test/users/login';
    const AUTHORIZED_REDIRECT = 'main/home/welcome';

    const PARAM_TYPES = [ 'string', 'int', 'mixed' ];

    /**
     * This method returns custom defined routes that map to existing ones.
     * 'uri' key keeps the existing route
     * 'params' keeps the needed uri params to call the action.
     * See below for example.
     * @return array
     */
    public static function getCustomMappings() {

        // Custom route without params
        // self::$_customMappings[ 'user/login' ] = [ 'uri' => 'test/users/login' ];

        // Custom route with params
        // self::$_customMappings[ 'user/logout/int/string' ] = [ 'uri' => 'test/users/logout', 'params' => [ 'int', 'string' ] ];

        return self::$_customMappings;
    }
}