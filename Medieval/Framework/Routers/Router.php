<?php

namespace Medieval\Framework\Routers;

use Medieval\Config\RoutingConfig;

class Router extends BaseRouter {

    public function __construct( $customRoutes ) {
        parent::__construct();

        $this->_customMappings = $customRoutes;
    }

    public function processRequestUri( $uri ) {
        if ( RoutingConfig::ROUTING_TYPE != 'default' ) {
            $result = $this->processCustomRequestUri( $uri );
        } else {
            $result = $this->processDefaultRequestUri( $uri );
        }

        return $result;
    }

    private function processDefaultRequestUri( $uri ) {
        $splitUri = explode( '/', trim( $uri, ' ' ) );

        if ( count( $splitUri ) < 2 ) {
            throw new \Exception( 'Less than 2 params' );
        }

        $firstParam = ucfirst( $splitUri[ 0 ] );
        $secondParam = $splitUri[ 1 ];

        if ( count( $splitUri ) == 2 ) {
            if ( isset( $this->appStructure[ $firstParam ] ) ) {
                throw new \Exception( 'No default route for this uri' );
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

        $this->validateRequestMethod();

        return new RequestUriResult(
            $this->getAreaName(),
            $this->getControllerName(),
            $this->getActionName(),
            $this->getAppStructure(),
            $this->getRequestParams()
        );
    }

    private function processCustomRequestUri( $uri ) {
        $uri = $this->matchAnnotationRoutes( $uri );
        $uri = $this->matchConfigRoutes( $uri );

        return $this->processDefaultRequestUri( $uri );
    }

    private function matchAnnotationRoutes( $uri ) {
        $uri = rtrim( $uri, '/ ' );
        $uriParts = explode( '/', $uri );

        foreach ( $this->appStructure as $areaKeys => $controllers ) {
            foreach ( $controllers as $controllerKey => $actions ) {
                foreach ( $actions as $actionKey => $actionValues ) {
                    if ( isset( $actionValues[ 'customRoute' ] ) ) {
                        $customUri = $actionValues[ 'customRoute' ][ 'uri' ];
                        $customUriParts = explode( '/', rtrim( $customUri, '/ ' ) );
                        $paramTypes = $actionValues[ 'customRoute' ][ 'params' ];
                        $method = $actionValues[ 'method' ];

                        if ( array_slice( $uriParts, 0, count( $customUriParts ) ) === $customUriParts ) {
                            if ( $_SERVER[ 'REQUEST_METHOD' ] == $method ) {
                                $requestParams = array_slice( $uriParts, count( $customUriParts ), count( $uriParts ) );

                                $this->validateRequestParams( $requestParams, $paramTypes );
                                $uri = $actionValues[ 'defaultRoute' ];

                                if ( !empty( $requestParams ) ) {
                                    $uri .= '/' . implode( '/', $requestParams );
                                }

                                return $uri;
                            }
                        }
                    }
                }
            }
        }

        return $uri;
    }

    private function matchConfigRoutes( $uri ) {
        $trimmedUri = rtrim( $uri, '/ ' );
        $explodedCustomUri = explode( '/', $trimmedUri );

        for ( $i = 2; $i < RoutingConfig::MAX_REQUEST_PARAMS; $i++ ) {
            $routeParts = array_slice( $explodedCustomUri, 0, $i );
            $routeImploded = implode( '/', array_slice( $explodedCustomUri, 0, $i ) );

            if ( isset( $this->_customMappings[ $routeImploded ][ 'uri' ] ) ) {
                $requestParams = [ ];

                if ( isset ( $this->_customMappings[ $routeImploded ][ 'params' ] ) ) {
                    $paramTypes = $this->_customMappings[ $routeImploded ][ 'params' ];
                    $requestParams = array_slice( $explodedCustomUri, count( $routeParts ) );
                    $this->validateRequestParams( $requestParams, $paramTypes );
                }

                $uri = $this->_customMappings[ $routeImploded ][ 'uri' ] . '/' . implode( '/', $requestParams );
                break;
            }
        }

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