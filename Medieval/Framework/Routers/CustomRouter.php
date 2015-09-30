<?php

namespace Medieval\Framework\Routers;

class CustomRouter extends BaseRouter {

    public function __construct() {
        parent::__construct();
    }

    public function processRequestUri( $uri ) {
        $splitUri = explode( '/', trim( $uri, ' ' ) );

        $upArea = ucfirst( $splitUri[ 0 ] );
        $upController = ucfirst( $splitUri[ 1 ] );
        $action = $splitUri[ 2 ];

        $nthParams = array_slice( $splitUri, 3 );


    }
}