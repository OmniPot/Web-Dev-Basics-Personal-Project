<?php

namespace Medieval\Framework\Routers;

class DefaultRouter extends BaseRouter {

    public function __construct() {
        parent::__construct();
    }

    public function processRequestUri( $uri ) {
        $splitUri = explode( '/', trim( $uri, ' ' ) );

        if ( count( $splitUri ) < 2 ) {
            throw new \Exception( 'Less than 2 params' );
        }

        $firstParam = ucfirst( $splitUri[ 0 ] );
        $secondParam = $splitUri[ 1 ];

        if ( count( $splitUri ) == 2 ) {
            if ( isset( $this->areas[ $firstParam ] ) ) {
                throw new \Exception( 'A controller name cannot be the same as an area name' );
            }

            $this->setControllerName( $firstParam );
            $this->setActionName( $secondParam );
        } else if ( count( $splitUri ) >= 3 ) {
            $thirdParam = array_slice( $splitUri, 2 );

            if ( !isset( $this->areas[ $firstParam ] ) ) {
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
            $this->getAreas(),
            $this->getRequestParams()
        );
    }
}