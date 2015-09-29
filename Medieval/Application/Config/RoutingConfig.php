<?php

namespace Medieval\Application\Config;

abstract class RoutingConfig {

    protected static $_mappings = array();

    const ROUTING_TYPE = 'custom';

    const DEFAULT_CONTROLLER = 'home';
    const DEFAULT_ACTION = 'welcome';

    const UNAUTHORIZED_REDIRECT = 'users/login';
    const AUTHORIZED_REDIRECT = 'home/welcome';

    public static function getMappings() {

        self::$_mappings[ 'tes' ][ 'user' ][ 'in' ] = [ 'area' => 'test', 'controller' => 'users', 'action' => 'login' ];
        self::$_mappings[ 'tes' ][ 'user' ][ 'out' ] = [ 'area' => 'test', 'controller' => 'users', 'action' => 'logout' ];
        self::$_mappings[ 'tes' ][ 'user' ][ 'reg' ] = [ 'area' => 'test', 'controller' => 'users', 'action' => 'register' ];

        self::$_mappings[ 'tes' ][ 'ho' ][ 'wel' ] = [ 'area' => 'test', 'controller' => 'home', 'action' => 'welcome' ];
        self::$_mappings[ 'tes' ][ 'ho' ][ 'not' ] = [ 'area' => 'test', 'controller' => 'home', 'action' => 'pageNotFound' ];
        return self::$_mappings;
    }
}