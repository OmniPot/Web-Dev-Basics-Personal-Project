<?php

namespace Medieval\Framework\Routers;

use Medieval\Config\RoutingConfig;

class Router extends BaseRouter {

    public function __construct() {
        parent::__construct();

        $this->_customMappings = RoutingConfig::getCustomMappings();
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

    private function matchAnnotationRoutes( $uri ) {
        foreach ( $this->appStructure as $areaKeys => $controllers ) {
            foreach ( $controllers as $controllerKeys => $actions ) {
                foreach ( $actions as $actionKeys => $actionValues ) {
                    $customRoute = $actionValues[ 'customRoute' ][ 'uri' ];
                    $realRoute = $actionValues[ 'defaultRoute' ];

                    if ( strpos( $uri, $customRoute ) === 0 ) {
                        $explodedCustomUri = explode( '/', $customRoute );
                        $explodedRequestRoute = explode( '/', rtrim( $uri, '/ ' ) );

                        $paramTypes = $actionValues[ 'customRoute' ][ 'params' ];
                        $requestParams = array_slice(
                            $explodedRequestRoute,
                            count( $explodedCustomUri ),
                            count( $paramTypes ) );

                        if ( count( $explodedRequestRoute ) > ( count( $explodedCustomUri ) + count( $paramTypes ) ) ) {
                            throw new \Exception( 'Invalid request parameters count' );
                        }

                        $this->validateRequestParams( $requestParams, $paramTypes );

                        $uri = $realRoute;

                        if ( !empty( $requestParams ) ) {
                            $uri .= '/' . implode( '/', $requestParams );
                        }
                    }
                }
            }
        }

        return $uri;
    }
}