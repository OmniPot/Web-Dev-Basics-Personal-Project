<?php

namespace Medieval\Framework;

use Medieval\Application\Config\RoutingConfig;

class FrontController {

    private static $_instance = null;

    private $_areas = array();

    private $_areaName;
    private $_customAreaName;

    private $_controller;
    private $_controllerName;
    private $_customControllerName;

    private $_actionName;
    private $_customActionName;

    private $_requestParams = [ ];

    private function __construct() {

    }

    public function dispatch() {
        if ( !$_GET || !isset( $_GET[ 'uri' ] ) ) {
            header( 'Location: ' . 'home' . '/' . 'welcome' );
            exit;
        }

        $trimmedUri = trim( $_GET[ 'uri' ], ' ' );
        $uri = explode( '/', $trimmedUri );

        try {
            $this->processRequestUri( $uri );

            $fullControllerName =
                'Medieval\\' . 'Application\\' . $this->_areaName . 'Area\\'
                . 'Controllers\\' . $this->_controllerName . 'Controller';

            $this->registerAreaControllers();
            $this->registerControllerActions();

            $this->validateUriRoute( $this->_areaName, $fullControllerName, $this->_actionName );

            $this->initController(
                $this->_areaName,
                $fullControllerName,
                $this->_customControllerName,
                $this->_requestParams );

            View::$areaName = $this->_areaName;
            View::$controllerName = $this->_controllerName;
            View::$actionName = $this->_actionName;

            call_user_func_array( [ $this->_controller, $this->_actionName ], $this->_requestParams );
        } catch ( \Exception $exception ) {
            echo $exception->getMessage();
        }
    }

    private function processRequestUri( $uri ) {
        if ( count( $uri ) >= 3 ) {
            $this->_areaName = ucfirst( trim( $uri[ 0 ] ) );
            $this->_customAreaName = trim( $uri[ 0 ] );

            $this->_controllerName = ucfirst( trim( $uri[ 1 ] ) );
            $this->_customControllerName = trim( $uri[ 1 ] );

            $this->_actionName = trim( $uri[ 2 ] );
            $this->_customActionName = trim( $uri[ 2 ] );

            $this->_requestParams = array_slice( $uri, 3 );

            if ( RoutingConfig::ROUTING_TYPE != 'default' ) {
                $routeMappings = RoutingConfig::getMappings();

                if ( !isset( $routeMappings[ $this->_customAreaName ] ) ) {
                    throw new \Exception( 'Custom area: ' . $this->_customAreaName . ' not found' );
                }
                if ( !isset( $routeMappings[ $this->_customAreaName ][ $this->_customControllerName ] ) ) {
                    throw new \Exception( 'Custom controller: '
                        . $this->_customControllerName . ' not found in area: ' . $this->_customAreaName );
                }
                if ( !isset( $routeMappings[ $this->_customAreaName ] ) ) {
                    throw new \Exception( 'Custom action: ' . $this->_customAreaName . ' not found in controller: '
                        . $this->_customControllerName . ' in area: ' . $this->_customAreaName );
                }

                $this->_areaName = ucfirst( $routeMappings
                [ $this->_customAreaName ]
                [ $this->_customControllerName ]
                [ $this->_customActionName ]
                [ 'area' ] );

                $this->_controllerName = ucfirst( $routeMappings
                [ $this->_customAreaName ]
                [ $this->_customControllerName ]
                [ $this->_customActionName ]
                [ 'controller' ] );

                $this->_actionName = $routeMappings
                [ $this->_customAreaName ]
                [ $this->_customControllerName ]
                [ $this->_customActionName ]
                [ 'action' ];
            }
        }
    }

    private function registerAreaControllers() {

        foreach ( glob( 'Application\\*Area' ) as $areaPath ) {
            if ( file_exists( $areaPath ) && is_readable( $areaPath ) ) {
                $areaName = str_replace( [ 'Application\\', 'Area' ], '', $areaPath );
                $this->_areas[ $areaName ] = [ ];

                foreach ( glob( 'Application\\' . $areaName . 'Area\\Controllers\\*Controller.php' ) as $controllerPath ) {
                    if ( file_exists( $areaPath ) && is_readable( $areaPath ) ) {
                        $fullPath = 'Medieval\\' . str_replace( '.php', '', $controllerPath );
                        $this->_areas[ $areaName ][ $fullPath ] = [ ];
                    } else {
                        throw new \Exception( 'File not found or is not readable: ' . $controllerPath );
                    }
                }
            } else {
                throw new \Exception( 'Directory not found: ' . $areaPath );
            }
        }
    }

    private function registerControllerActions() {
        foreach ( $this->_areas as $area => $areaControllers ) {
            foreach ( $areaControllers as $areaController => $areaMethods ) {
                $methods = array_values( get_class_methods( $areaController ) );
                foreach ( $methods as $method ) {
                    $this->_areas[ $area ][ $areaController ][ $method ] = true;
                }
            }
        }
    }

    private function initController( $areaName, $controllerName, $customControllerName, array $requestParams ) {
        if ( isset( $this->_areas[ $areaName ][ $controllerName ] ) ) {
            $this->_controller = new $controllerName( $controllerName, $customControllerName, $requestParams );
        } else {
            throw new \Exception( 'Controller not found: ' . $controllerName );
        }
    }

    private function validateUriRoute( $areaName, $fullControllerName, $actionName ) {
        if ( !isset( $this->_areas[ $areaName ] ) ) {
            throw new \Exception( 'Area: ' . $areaName . ' not found.' );
        }

        if ( !isset( $this->_areas[ $areaName ][ $fullControllerName ] ) ) {
            throw new \Exception( 'Controller: ' . $fullControllerName . ' not found.' );
        }

        if ( !isset( $this->_areas[ $areaName ][ $fullControllerName ][ $actionName ] ) ) {
            throw new \Exception(
                'Controller: ' . $fullControllerName . '
                    contains no method: ' . $actionName );
        }
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}