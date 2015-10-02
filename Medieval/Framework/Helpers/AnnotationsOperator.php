<?php

namespace Medieval\Framework\Helpers;

use Medieval\Config\RoutingConfig;

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

    public static function parseActionDoc( $doc ) {
        $resultArray = [ ];
        $routeRegex = '/@route\((?:\'|\")(.*)(?:\'|\")\)/';
        $methodRegex = '/@(GET|POST|PUT|DELETE)\s+/';

        if ( preg_match( $routeRegex, $doc, $routeMatches ) ) {
            $resultArray = self::parseRoute( $routeMatches, $resultArray );
        }

        $resultArray[ 'method' ] = 'GET';
        if ( preg_match( $methodRegex, $doc, $methodMatches ) ) {
            $resultArray[ 'method' ] = $methodMatches[ 1 ];
        }

        return $resultArray;
    }

    private static function parseRoute( $routeMatches, $resultArray ) {
        $exploded = explode( '/', $routeMatches[ 1 ] );

        $routeResult = [ 'uri' => '', 'params' => [ ] ];
        foreach ( $exploded as $key ) {
            if ( in_array( $key, RoutingConfig::PARAM_TYPES ) ) {
                $routeResult[ 'params' ][] = $key;
            } else {
                $routeResult[ 'uri' ] .= "$key/";
            }
        }

        $routeResult[ 'uri' ] = rtrim( $routeResult[ 'uri' ], '/' );
        $resultArray[ 'customRoute' ] = $routeResult;

        return $resultArray;
    }
}