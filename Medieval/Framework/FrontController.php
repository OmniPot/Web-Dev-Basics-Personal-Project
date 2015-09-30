<?php

namespace Medieval\Framework;

use Medieval\Config\RoutingConfig;

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
            header( 'Location: ' . 'test/' . 'home/' . 'welcome' );
            exit;
        }

        $trimmedUri = trim( $_GET[ 'uri' ], ' ' );
        $uri = explode( '/', $trimmedUri );

        try {
            $this->registerAreasControllers();
            $this->registerControllersActions();

            $this->processRequestUri( $uri );

            $fullControllerName =
                'Medieval\\' . 'Areas\\' . $this->_areaName . 'Area\\'
                . 'Controllers\\' . $this->_controllerName . 'Controller';

            $this->validateUriRoute( $this->_areaName, $fullControllerName, $this->_actionName );

            $this->initController( $fullControllerName );

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

            $this->_actionName = $this->_customActionName = trim( $uri[ 2 ] );
            $this->_requestParams = array_slice( $uri, 3 );

            if ( RoutingConfig::ROUTING_TYPE != 'default' ) {
                $routeMappings = RoutingConfig::getMappings();

                if ( isset( $routeMappings[ $this->_customAreaName ][ $this->_customControllerName ][ $this->_customActionName ] ) ) {
                    $this->_areaName = ucfirst( $routeMappings
                    [ $this->_customAreaName ][ $this->_customControllerName ][ $this->_customActionName ][ 'area' ] );
                    $this->_controllerName = ucfirst( $routeMappings
                    [ $this->_customAreaName ][ $this->_customControllerName ][ $this->_customActionName ][ 'controller' ] );
                    $this->_actionName = $routeMappings
                    [ $this->_customAreaName ][ $this->_customControllerName ][ $this->_customActionName ][ 'action' ];
                }
            }
        }
    }

    private function registerAreasControllers() {
        foreach ( glob( 'Areas\\*Area' ) as $areaPath ) {
            if ( file_exists( $areaPath ) && is_readable( $areaPath ) ) {
                $areaName = ucfirst( str_replace( [ 'Areas\\', 'Area' ], '', $areaPath ) );
                $this->_areas[ $areaName ] = [ ];

                foreach ( glob( 'Areas\\' . $areaName . 'Area\\Controllers\\*Controller.php' ) as $controllerPath ) {
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

    private function registerControllersActions() {
        foreach ( $this->_areas as $area => $areaControllers ) {
            foreach ( $areaControllers as $areaController => $areaMethods ) {
                $methods = array_values( get_class_methods( $areaController ) );
                foreach ( $methods as $method ) {
                    $this->_areas[ $area ][ $areaController ][ $method ] = true;
                }
            }
        }
    }

    private function validateUriRoute( $areaName, $fullControllerName, $actionName ) {
        if ( !isset( $this->_areas[ $areaName ] ) ) {
            throw new \Exception( 'Area: ' . $areaName . ' not found.' );
        }

        if ( !isset( $this->_areas[ $areaName ][ $fullControllerName ] ) ) {
            throw new \Exception( "Controller: $fullControllerName not found int area: $this->_areaName" );
        }

        if ( !isset( $this->_areas[ $areaName ][ $fullControllerName ][ $actionName ] ) ) {
            throw new \Exception(
                'Controller: ' . $fullControllerName . '
                    contains no method: ' . $actionName );
        }
    }

    private function initController( $controllerName ) {
        if ( !isset( $this->_areas[ $this->_areaName ][ $controllerName ] ) ) {
            throw new \Exception( 'Controller not found: ' . $controllerName );
        }

        $this->_controller = new $controllerName(
            $this->_customAreaName,
            $this->_customControllerName,
            $this->_customActionName,
            $this->_requestParams );
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}