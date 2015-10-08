<?php

namespace Medieval\Framework\Routers;

use Medieval\Config\RoutingConfig;

class Router extends BaseRouter {

    const STRING_VALIDATION_REGEX = '/^[a-zA-Z\_\-]+$/';
    const INT_VALIDATION_REGEX = '/^[0-9]+$/';
    const MIXED_VALIDATION_REGEX = '/^[a-zA-Z0-9\_\,\-]+$/';

    private $_appStructure;
    private $_actionsArray;

    public function __construct( $_appStructure, $_actionsArray ) {
        parent::__construct();

        $this->setAppStructure( $_appStructure );
        $this->setActionsArray( $_actionsArray );

        $this->_customMappings = RoutingConfig::getCustomMappings();
    }

    public function getAppStructure() {
        return $this->_appStructure;
    }

    public function setAppStructure( $appStructure ) {
        $this->_appStructure = $appStructure;
    }

    public function getActionsArray() {
        return $this->_actionsArray;
    }

    public function setActionsArray( $actionsArray ) {
        $this->_actionsArray = $actionsArray;
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
            throw new \Exception( 'No valid route found', 404 );
        }

        $firstParam = ucfirst( $splitUri[ 0 ] );
        $secondParam = $splitUri[ 1 ];
        if ( count( $splitUri ) == 2 ) {
            if ( isset( $this->getAppStructure()[ $firstParam ] ) ) {
                throw new \Exception( 'No valid route found', 404 );
            }

            $this->setControllerName( $firstParam );
            $this->setActionName( $secondParam );
        } else if ( count( $splitUri ) >= 3 ) {

            $thirdParam = array_values( array_slice( $splitUri, 2 ) );
            if ( !isset( $this->getAppStructure()[ $firstParam ] ) ) {
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
        $uri = $this->matchCustomRoutes( $this->getActionsArray(), $uri );
        $uri = $this->matchCustomRoutes( $this->_customMappings, $uri );

        return $this->processDefaultRequestUri( $uri );
    }

    private function matchCustomRoutes( $collection, $uri ) {
        $uri = rtrim( $uri, '/ ' );
        $uriParts = explode( '/', $uri );

        foreach ( $collection as $key => $value ) {
            if ( empty( $value ) ) {
                continue;
            }

            $customUri = $value[ 'route' ][ 'uri' ];
            $paramTypes = $value[ 'route' ][ 'params' ];

            $customUriParts = explode( '/', rtrim( $customUri, '/ ' ) );
            $uriMatch = array_slice( $uriParts, 0, count( $customUriParts ) ) == $customUriParts;

            if ( $uriMatch ) {

                if ( !$this->validateActionRestrictions( $value[ 'method' ], $value[ 'authorize' ], $value[ 'admin' ] ) ) {
                    continue;
                }

                $requestParams = array_slice(
                    $uriParts, count( $customUriParts ), count( $uriParts ) );

                $this->validateRequestParams( $requestParams, $paramTypes );
                $uri = $value[ 'defaultRoute' ];

                if ( !empty( $requestParams ) ) {
                    $uri .= '/' . implode( '/', $requestParams );
                }

                return $uri;
            }
        }

        return $uri;
    }

    private function validateActionRestrictions( $actionRequestMethod, $requiredUser, $requiredAdmin ) {
        if ( $this->_requestMethod != $actionRequestMethod ) {
            return false;
        }

        $requiredAuthLevel = 'guest';
        if ( $requiredUser ) {
            $requiredAuthLevel = 'user';
        }
        if ( $requiredAdmin ) {
            $requiredAuthLevel = 'admin';
        }

        if ( ( $requiredAuthLevel == 'admin' && $this->_userRole != 'admin' ) ||
            ( $requiredAuthLevel == 'user' && ( $this->_userRole != 'admin' && $this->_userRole != 'user' ) )
        ) {
            throw new \Exception( 'Unauthorized access' );
        }

        return true;
    }

    private function validateRequestParams( $requestParams, $paramTypes ) {
        if ( count( $requestParams ) != count( $paramTypes ) ) {
            throw new \Exception( 'Invalid request parameters count' );
        }

        for ( $i = 0; $i < count( $paramTypes ); $i++ ) {
            $typeMatches = false;

            switch ( $paramTypes[ $i ] ) {
                case 'string' :
                    $typeMatches = preg_match( self::STRING_VALIDATION_REGEX, $requestParams[ $i ] );
                    break;
                case 'int' :
                    $typeMatches = preg_match( self::INT_VALIDATION_REGEX, $requestParams[ $i ] );
                    break;
                case 'mixed' :
                    $typeMatches = preg_match( self::MIXED_VALIDATION_REGEX, $requestParams[ $i ] );
                    break;
            }

            if ( !$typeMatches ) {
                throw new \Exception( "Invalid parameter type for $requestParams[$i] expected $paramTypes[$i]" );
            };
        }
    }
}