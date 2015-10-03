<?php

namespace Medieval\Framework\Helpers;

class AnnotationParser {

    private static $paramTypes = [ 'string', 'int', 'mixed' ];

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
        $methodRegex = '/@method\s+(POST|GET|PUT|DELETE)\s+/';

        if ( preg_match( $routeRegex, $doc, $routeMatches ) ) {
            $resultArray = self::parseRoute( $routeMatches, $resultArray );
        }

        if ( preg_match( $methodRegex, $doc, $methodMatches ) ) {
            $resultArray[ 'method' ] = $methodMatches[ 1 ];
        } else {
            $resultArray[ 'method' ] = 'GET';
        }

        return $resultArray;
    }

    private static function parseRoute( $routeMatches, $resultArray ) {
        $exploded = explode( '/', $routeMatches[ 1 ] );

        $routeResult = [ 'uri' => '', 'params' => [ ] ];
        foreach ( $exploded as $key ) {
            if ( in_array( $key, self::$paramTypes ) ) {
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