<?php

namespace Medieval\Framework\Helpers;

class AnnotationParser {

    private static $paramTypes = [ 'string', 'int', 'mixed' ];

    const ROUTE_REGEX = '/@(route)\((?:\'|\")(.*)(?:\'|\")\)/';
    const METHOD_REGEX = '/@(method)\s+(POST||PUT|DELETE)/';
    const ADMIN_REGEX = '/@(admin)/';
    const AUTHORIZE_REGEX = '/@(authorize)/';

    const DEFAULT_METHOD = 'GET';
    const DEFAULT_AUTHORIZE = 0;
    const DEFAULT_ADMIN = 0;

    private static $docRegexes = [
        self::ROUTE_REGEX,
        self::METHOD_REGEX,
        self::ADMIN_REGEX,
        self::AUTHORIZE_REGEX
    ];

    /**
     * @param \ReflectionMethod $method
     * @return \string[] $docComments
     */
    public static function getActionDoc( \ReflectionMethod $method ) {
        if ( $method ) {
            $docComment = $method->getDocComment();
            if ( $docComment ) {
                return $docComment;
            }
        }

        return false;
    }

    public static function parseActionDoc( $doc ) {
        $resultArray = [ ];

        foreach ( self::$docRegexes as $regex ) {
            preg_match( $regex, $doc, $routeMatches );
            if ( $routeMatches ) {
                $parseMethod = 'parse' . ucfirst( $routeMatches[ 1 ] );
                $resultArray[ $routeMatches[ 1 ] ] = self::$parseMethod( $routeMatches );
            }
        }

        if ( !isset( $resultArray[ 'method' ] ) || !$resultArray[ 'method' ] ) {
            $resultArray[ 'method' ] = self::DEFAULT_METHOD;
        }

        if ( !isset( $resultArray[ 'route' ] ) ) {
            $resultArray[ 'route' ] = [ 'uri' => '', 'params' => [ ] ];
        }

        $resultArray[ 'admin' ] = isset( $resultArray[ 'admin' ] ) ? true : false;
        $resultArray[ 'authorize' ] = isset( $resultArray[ 'authorize' ] ) ? true : false;

        return $resultArray;
    }

    private static function parseRoute( $matches ) {
        $exploded = explode( '/', $matches[ 2 ] );
        $routeResult = [ 'uri' => '', 'params' => [ ] ];

        foreach ( $exploded as $key ) {
            if ( in_array( $key, self::$paramTypes ) ) {
                $routeResult[ 'params' ][] = $key;
            } else {
                $routeResult[ 'uri' ] .= "$key/";
            }
        }

        $routeResult[ 'uri' ] = rtrim( $routeResult[ 'uri' ], '/' );

        return $routeResult;
    }

    private static function parseMethod( $matches ) {
        return $matches[ 2 ];
    }

    private static function parseAdmin( $matches ) {
        return isset( $matches[ 1 ] ) ? true : false;
    }

    private static function parseAuthorize( $matches ) {
        return isset( $matches[ 1 ] ) ? true : false;
    }
}