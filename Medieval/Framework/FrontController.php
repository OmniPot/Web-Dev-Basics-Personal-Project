<?php

namespace Medieval\Framework;

use Medieval\Config\AppConfig;
use Medieval\Config\BaseRoutingConfig;

use Medieval\Framework\Routers\Router;

class FrontController {

    private static $_instance = null;

    private $_controller;

    /** @var Routers\Router $_router */
    private $_router;

    /** @var Routers\RequestUriResult $_uriParseResult */
    private $_uriParseResult;

    private function __construct() {
        $this->_router = new Router();
    }

    public function dispatch() {
        if ( !$_GET || !isset( $_GET[ 'uri' ] ) ) {
            header( 'Location: ' . BaseRoutingConfig::AUTHORIZED_REDIRECT );
            exit;
        }

        try {
            $this->_uriParseResult = $this->_router->processRequestUri( $_GET[ 'uri' ] );

            $fullControllerName = $this->getFullControllerName(
                $this->_uriParseResult->getAreaName(),
                $this->_uriParseResult->getControllerName()
            );

            $this->validateUriRoute(
                $this->_uriParseResult->getAreaName(),
                $fullControllerName,
                $this->_uriParseResult->getActionName()
            );

            $this->initController( $fullControllerName );

            View::$areaName = $this->_uriParseResult->getAreaName();
            View::$controllerName = $this->_uriParseResult->getControllerName();
            View::$actionName = $this->_uriParseResult->getActionName();

            call_user_func_array(
                [
                    $this->_controller,
                    $this->_uriParseResult->getActionName()
                ],
                $this->_uriParseResult->getRequestParams() );

        } catch ( \Exception $exception ) {
            echo $exception->getMessage();
        }
    }

    protected function validateUriRoute( $areaName, $fullControllerName, $actionName ) {
        if ( !isset( $this->_uriParseResult->getAppStructure()[ $areaName ] ) ) {
            throw new \Exception( "Area: $areaName not found." );
        }

        if ( !isset( $this->_uriParseResult->getAppStructure()[ $areaName ][ $fullControllerName ] ) ) {
            throw new \Exception( "Controller: $fullControllerName not found in area: " . $this->_uriParseResult->getAreaName() );
        }

        if ( !isset( $this->_uriParseResult->getAppStructure()[ $areaName ][ $fullControllerName ][ $actionName ] ) ) {
            throw new \Exception(
                "Controller: $fullControllerName contains no method: $actionName" );
        }
    }

    private function initController( $controllerName ) {
        if ( !$controllerName ) {
            throw new \Exception( 'Controller name cannot be null' );
        }

        if ( !isset(
            $this->_uriParseResult->getAppStructure()[ $this->_uriParseResult->getAreaName() ][ $controllerName ] )
        ) {
            throw new \Exception( "Controller not found: $controllerName" );
        }

        $this->_controller = new $controllerName(
            $this->_uriParseResult->getAreaName(),
            $this->_uriParseResult->getControllerName(),
            $this->_uriParseResult->getActionName(),
            $this->_uriParseResult->getRequestParams()
        );
    }

    private function getFullControllerName( $areaName, $controllerName ) {
        if ( !$areaName ) {
            throw new \Exception( 'No area name to get the controller name from' );
        }
        if ( !$controllerName ) {
            throw new \Exception( 'No controller name to process' );
        }

        $fullControllerName = AppConfig::VENDOR_NAMESPACE;

        if ( $areaName != AppConfig::DEFAULT_AREA ) {
            $fullControllerName .= AppConfig::AREAS_NAMESPACE . $areaName . AppConfig::AREA_SUFFIX;
        }

        $fullControllerName .= AppConfig::CONTROLLERS_NAMESPACE . $controllerName . AppConfig::CONTROLLER_SUFFIX;

        return $fullControllerName;
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}