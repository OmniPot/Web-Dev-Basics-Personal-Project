<?php

namespace Medieval\Framework\Routers;

use Medieval\Config\BaseRoutingConfig;

class Router extends BaseRouter {

    public function __construct() {
        parent::__construct();

        $this->_customMappings = BaseRoutingConfig::getCustomMappings();
        $this->_annotationMappings = BaseRoutingConfig::getAnnotationMappings();
    }

    public function processRequestUri( $uri ) {
        if ( BaseRoutingConfig::ROUTING_TYPE != 'default' ) {
            $result = $this->processCustomRequestUri( $uri );
        } else {
            $result = $this->processDefaultRequestUri( $uri );
        }

        return $result;
    }

    public function processDefaultRequestUri( $uri ) {
        $splitUri = explode( '/', trim( $uri, ' ' ) );

        if ( count( $splitUri ) < 2 ) {
            throw new \Exception( 'Less than 2 params' );
        }

        $firstParam = ucfirst( $splitUri[ 0 ] );
        $secondParam = $splitUri[ 1 ];

        if ( count( $splitUri ) == 2 ) {
            if ( isset( $this->appStructure[ $firstParam ] ) ) {
                throw new \Exception( 'A controller name cannot be the same as an area name' );
            }

            $this->setControllerName( $firstParam );
            $this->setActionName( $secondParam );
        } else if ( count( $splitUri ) >= 3 ) {
            $thirdParam = array_values( array_slice( $splitUri, 2 ) );

            if ( !isset( $this->appStructure[ $firstParam ] ) ) {
                $this->setControllerName( $firstParam );
                $this->setActionName( $secondParam );
                $this->setRequestParams( $thirdParam );
            } else {
                $thirdParam = $splitUri[ 2 ];
                $fourthParam = array_slice( $splitUri, 3 );

                $this->setAreaName( $firstParam );
                $this->setControllerName( ucfirst( $secondParam ) );
                $this->setActionName( $thirdParam );
                $this->setRequestParams( $fourthParam );
            }
        };

        return new RequestUriResult(
            $this->getAreaName(),
            $this->getControllerName(),
            $this->getActionName(),
            $this->getAppStructure(),
            $this->getRequestParams()
        );
    }

    private function processCustomRequestUri( $uri ) {
        $exploded = explode( '/', trim( $uri, '/ ' ) );

        $doubleRoute = implode( '/', array_slice( $exploded, 0, 2 ) );
        $tripleRoute = implode( '/', array_slice( $exploded, 0, 3 ) );

        $mainParamsCount = 2;
        if ( isset( $this->_annotationMappings[ $doubleRoute ][ 'uri' ] ) ) {
            $uri = $this->extractRegularUri( $doubleRoute, $this->_annotationMappings, $exploded, $mainParamsCount );
        } else if ( isset( $this->_annotationMappings[ $tripleRoute ][ 'uri' ] ) ) {
            $mainParamsCount = 3;
            $uri = $this->extractRegularUri( $tripleRoute, $this->_annotationMappings, $exploded, $mainParamsCount );
        } else if ( isset( $this->_customMappings[ $doubleRoute ][ 'uri' ] ) ) {
            $uri = $this->extractRegularUri( $doubleRoute, $this->_customMappings, $exploded, $mainParamsCount );
        } else if ( isset( $this->_customMappings[ $tripleRoute ][ 'uri' ] ) ) {
            $mainParamsCount = 3;
            $uri = $this->extractRegularUri( $tripleRoute, $this->_customMappings, $exploded, $mainParamsCount );
        }

        return $this->processDefaultRequestUri( $uri );
    }

    private function extractRegularUri( $route, array $collection, $uriParts, $mainParamsCount ) {
        if ( isset ( $collection[ $route ][ 'params' ] ) ) {
            $requestParams = array_slice( $uriParts, $mainParamsCount );
            $paramTypes = $collection[ $route ][ 'params' ];
            $this->validateRequestParams( $requestParams, $paramTypes );
        }

        $uri = $collection[ $route ][ 'uri' ] . '/' . implode( '/', $requestParams );
        return $uri;
    }

    private function validateRequestParams( $requestParams, $paramTypes ) {
        if ( count( $requestParams ) != count( $paramTypes ) ) {
            throw new \Exception( 'Invalid request parameters count' );
        }

        for ( $i = 0; $i < count( $paramTypes ); $i++ ) {
            $typeMatches = false;

            switch ( $paramTypes[ $i ] ) {
                case 'string' :
                    $typeMatches = preg_match( '/^[a-zA-Z\_\-]+$/', $requestParams[ $i ] );
                    break;
                case 'int' :
                    $typeMatches = preg_match( '/^[0-9]+$/', $requestParams[ $i ] );
                    break;
                case 'mixed' :
                    $typeMatches = preg_match( '/^[a-zA-Z0-9\_\,\-]+$/', $requestParams[ $i ] );
                    break;
            }

            if ( !$typeMatches ) {
                throw new \Exception( "Invalid parameter type for $requestParams[$i] expected $paramTypes[$i]" );
            };
        }
    }
}