<?php

namespace Medieval\Config;

class BaseRoutingConfig {

    protected static $_mappings = array();

    const ROUTING_TYPE = 'custom';

    const UNAUTHORIZED_REDIRECT = 'test/users/login';
    const AUTHORIZED_REDIRECT = 'main/home/welcome';

    const CUSTOM_UNAUTHORIZED_REDIRECT = 'tes/user/in';
    const CUSTOM_AUTHORIZED_REDIRECT = 'ho/wel';

    public static function getMappings() {

        self::$_mappings[ 'tes' ][ 'user' ][ 'in' ] = [ 'area' => 'test', 'controller' => 'users', 'action' => 'login' ];
        self::$_mappings[ 'tes' ][ 'user' ][ 'in' ] = [ 'area' => 'test', 'controller' => 'users', 'action' => 'login' ];
        self::$_mappings[ 'tes' ][ 'user' ][ 'out' ] = [ 'area' => 'test', 'controller' => 'users', 'action' => 'logout' ];
        self::$_mappings[ 'tes' ][ 'user' ][ 'reg' ] = [ 'area' => 'test', 'controller' => 'users', 'action' => 'register' ];

        self::$_mappings[ 'tes' ][ 'ho' ][ 'wel' ] = [ 'area' => 'test', 'controller' => 'home', 'action' => 'welcome' ];
        self::$_mappings[ 'ho' ][ 'wel' ] = [ 'area' => 'main', 'controller' => 'home', 'action' => 'welcome' ];

        return self::$_mappings;
    }
}