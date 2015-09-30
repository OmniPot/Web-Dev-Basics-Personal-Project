<?php

namespace Medieval\Framework\Routers;

use Medieval\Config\BaseRoutingConfig;

class CustomRouter extends BaseRouter {

    private $_mappings = array();

    public function __construct() {
        parent::__construct();

        $this->_mappings = BaseRoutingConfig::getMappings();
    }

    public function processRequestUri( $uri ) {
        $splitUri = explode( '/', trim( $uri, ' ' ) );

        if ( count( $splitUri ) < 2 ) {
            throw new \Exception( 'Less than 2 params' );
        }

        $firstParam = $splitUri[ 0 ];
        $secondParam = $splitUri[ 1 ];

        if ( count( $splitUri ) == 2 ) {
            if ( !isset( $this->_mappings[ $firstParam ][ $secondParam ] ) ) {
                throw new \Exception( 'No custom route found for the uri: ' . $uri );
            }

            $firstCustom = ucfirst( $this->_mappings[ $firstParam ][ $secondParam ][ 'controller' ] );
            $secondCustom = $this->_mappings[ $firstParam ][ $secondParam ][ 'action' ];

            $this->setControllerName( $firstCustom );
            $this->setActionName( $secondCustom );
        } else if ( count( $splitUri ) >= 3 ) {
            $thirdParam = $splitUri[ 2 ];

            if ( isset( $this->_mappings[ $firstParam ][ $secondParam ][ $thirdParam ] ) ) {
                $firstCustom = $this->_mappings[ $firstParam ][ $secondParam ][ $thirdParam ][ 'area' ];
                $secondCustom = $this->_mappings[ $firstParam ][ $secondParam ][ $thirdParam ][ 'controller' ];
                $thirdCustom = $this->_mappings[ $firstParam ][ $secondParam ][ $thirdParam ][ 'action' ];
                $fourthParam = array_slice( $splitUri, 3 );

                $this->setAreaName( ucfirst( $firstCustom ) );
                $this->setControllerName( ucfirst( $secondCustom ) );
                $this->setActionName( $thirdCustom );
                $this->setRequestParams( $fourthParam );

            } else if ( isset( $this->_mappings[ $firstParam ][ $secondParam ] ) ) {

                $firstCustom = $this->_mappings[ $firstParam ][ $secondParam ][ 'area' ];
                $secondCustom = $this->_mappings[ $firstParam ][ $secondParam ][ 'controller' ];
                $thirdCustom = $this->_mappings[ $firstParam ][ $secondParam ][ 'action' ];
                $fourthParam = array_slice( $splitUri, 2 );

                $this->setAreaName( ucfirst( $firstCustom ) );
                $this->setControllerName( ucfirst( $secondCustom ) );
                $this->setActionName( $thirdCustom );
                $this->setRequestParams( $fourthParam );
            } else {
                throw new \Exception( 'No custom route found for the uri: ' . $uri );
            }
        };

        return new RequestUriResult(
            $this->getAreaName(),
            $this->getControllerName(),
            $this->getActionName(),
            $this->getAreas(),
            $this->getRequestParams()
        );
    }
}