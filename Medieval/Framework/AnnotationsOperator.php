<?php

namespace Medieval\Framework;

use Medieval\Config\BaseRoutingConfig;

class AnnotationsOperator {

    /**
     * @param $controllerPath
     * @return \ReflectionClass
     */
    public static function getReflectionClass( $controllerPath ) {
        return new \ReflectionClass( $controllerPath );
    }

    /**
     * @param \ReflectionClass $class
     * @return \ReflectionMethod[]
     */
    public static function getClassActions( \ReflectionClass $class ) {
        return $class->getMethods();
    }

    /**
     * @param \ReflectionMethod[] $methods
     * @return string[] $docComments
     */
    public static function getActionRoutes( array $methods ) {
        if ( count( $methods ) ) {
            $docComments = [ ];
            foreach ( $methods as $key => $value ) {
                $docComment = $value->getDocComment();
                if ( $docComment ) {
                    $docComments[ $value->name ] = $docComment;
                }
            }

            return $docComments;
        }

        return false;
    }

    public static function parseActionRoute( $route ) {
        $regex = '/@route\(\'(.*)\'\)/';

        if ( preg_match( $regex, $route, $routeMatches ) ) {
            $baseRoute = $routeMatches[ 1 ];
            $exploded = explode( '/', $baseRoute );

            $result = [ 'uri' => '', 'params' => [ ] ];
            foreach ( $exploded as $key ) {
                if ( in_array( $key, BaseRoutingConfig::PARAM_TYPES ) ) {
                    $result[ 'params' ][] = $key;
                } else {
                    $result[ 'uri' ] .= "$key/";
                }
            }

            $result[ 'uri' ] = rtrim( $result[ 'uri' ], '/' );
            return $result;
        }

        return false;
    }
}