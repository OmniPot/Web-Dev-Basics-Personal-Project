<?php

namespace Medieval\Config;

class BaseRoutingConfig {

    private static $_customMappings = array();
    private static $_annotationMappings = array();

    const ROUTING_TYPE = 'custom';

    const UNAUTHORIZED_REDIRECT = 'test/users/login';
    const AUTHORIZED_REDIRECT = 'main/home/welcome';

    const PARAM_TYPES = [ 'string', 'int', 'mixed' ];

    public static function getCustomMappings() {

        self::$_customMappings[ 'tes/user/in' ] = [ 'uri' => 'test/users/login' ];
        self::$_customMappings[ 'tes/user/out' ] = [ 'uri' => 'test/users/logout' ];
        self::$_customMappings[ 'tes/user/reg' ] = [ 'uri' => 'test/users/register' ];

        self::$_customMappings[ 'tes/ho/wel' ] = [ 'uri' => 'home/welcome' ];
        self::$_customMappings[ 'ho/wel' ] = [ 'uri' => 'home/welcome' ];

        return self::$_customMappings;
    }

    public static function getAnnotationMappings() {
        return self::$_annotationMappings;
    }

    public static function setAnnotationMapping( $customRoute, $realRoute, $parameters = [ ] ) {
        self::$_annotationMappings[ $customRoute ] = [ 'uri' => $realRoute, 'params' => $parameters ];
    }
}