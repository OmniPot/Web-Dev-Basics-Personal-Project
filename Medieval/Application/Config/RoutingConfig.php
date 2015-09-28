<?php

namespace Medieval\Application\Config;

class RoutingConfig {

    private static $_mappings = array();

    const ROUTER_TYPE = 'custom';

    const DEFAULT_CONTROLLER = 'ho';
    const DEFAULT_ACTION = 'wel';

    const UNAUTHORIZED_REDIRECT = 'user/in';
    const AUTHORIZED_REDIRECT = 'ho/wel';

    public static function getMappings() {

        self::$_mappings[ 'user' ][ 'in' ] = [ 'controller' => 'users', 'action' => 'login' ];
        self::$_mappings[ 'user' ][ 'out' ] = [ 'controller' => 'users', 'action' => 'logout' ];
        self::$_mappings[ 'user' ][ 'reg' ] = [ 'controller' => 'users', 'action' => 'register' ];

        self::$_mappings[ 'ho' ][ 'wel' ] = [ 'controller' => 'home', 'action' => 'welcome' ];
        self::$_mappings[ 'ho' ][ 'not' ] = [ 'controller' => 'home', 'action' => 'pageNotFound' ];

        return self::$_mappings;
    }
}