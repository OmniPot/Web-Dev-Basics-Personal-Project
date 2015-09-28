<?php

namespace Medieval\Framework;

use Medieval\Application\Config\MainConfig;
use Medieval\Application\Config\RoutingConfig;

class FrontController {

    private static $_instance = null;

    private $controllers = array();
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
            header( 'Location: ' . RoutingConfig::DEFAULT_CONTROLLER . '/' . RoutingConfig::DEFAULT_ACTION );
            exit;
        }

        $trimmedUri = trim( $_GET[ 'uri' ], ' ' );
        $uri = explode( '/', $trimmedUri );

        try {
            $this->processRequestUri( $uri );

            $fullControllerName =
                MainConfig::VENDOR_NAMESPACE
                . MainConfig::APPLICATION_NAMESPACE
                . MainConfig::CONTROLLERS_NAMESPACE
                . ucfirst( $this->_controllerName )
                . MainConfig::CONTROLLERS_SUFFIX;

            $this->registerControllers();
            $this->registerControllerActions();

            $this->validateController( $fullControllerName );
            $this->validateControllerAction( $fullControllerName, $this->_actionName );

            $this->initController( $fullControllerName, $this->_customControllerName, $this->_requestParams );

            View::$controllerName = $this->_controllerName;
            View::$actionName = $this->_actionName;

            call_user_func_array( [ $this->_controller, $this->_actionName ], $this->_requestParams );
        } catch ( \Exception $exception ) {
            echo $exception->getMessage();
        }
    }

    private function processRequestUri( $uri ) {
        if ( count( $uri ) >= 2 ) {
            $this->_customControllerName = $this->_controllerName = trim( $uri[ 0 ] );
            $this->_customActionName = $this->_actionName = trim( $uri[ 1 ] );
            $this->_requestParams = array_slice( $uri, 2 );

            if ( RoutingConfig::ROUTER_TYPE != 'default' ) {
                $routeMappings = RoutingConfig::getMappings();
                if ( isset( $routeMappings[ $this->_customControllerName ][ $this->_customActionName ] ) ) {
                    $this->_controllerName = $routeMappings[ $this->_customControllerName ][ $this->_customActionName ][ 'controller' ];
                    $this->_actionName = $routeMappings[ $this->_customControllerName ][ $this->_customActionName ][ 'action' ];
                }
            }
        }
    }

    private function registerControllers() {
        foreach ( glob( MainConfig::CONTROLLERS_FOLDER ) as $filePath ) {
            if ( file_exists( $filePath ) && is_readable( $filePath ) ) {
                $fileFullPath = MainConfig::VENDOR_NAMESPACE . str_replace( [ '/', '\\' ], DIRECTORY_SEPARATOR, $filePath );
                $fileFullPath = substr( $fileFullPath, 0, -4 );
                $this->controllers[ $fileFullPath ] = true;
            } else {
                throw new \Exception( 'File not found: ' . $filePath );
            }
        }
    }

    private function registerControllerActions() {
        foreach ( $this->controllers as $cKey => $cValue ) {
            $methods = array_values( get_class_methods( $cKey ) );
            $this->controllers[ $cKey ] = array();
            foreach ( $methods as $method ) {
                $this->controllers[ $cKey ][ $method ] = true;
            }
        }
    }

    private function initController( $controllerName, $customControllerName, array $requestParams ) {
        if ( isset( $this->controllers[ $controllerName ] ) ) {
            $this->_controller = new $controllerName( $controllerName, $customControllerName, $requestParams );
        } else {
            throw new \Exception( 'File not found: ' . $controllerName );
        }
    }

    private function validateController( $fullControllerName ) {
        if ( !isset( $this->controllers[ $fullControllerName ] ) ) {
            throw new \Exception( 'Controller: ' . $fullControllerName . ' not found.' );
        }
    }

    private function validateControllerAction( $fullControllerName, $actionName ) {
        if ( !isset( $this->controllers[ $fullControllerName ][ $actionName ] ) ) {
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