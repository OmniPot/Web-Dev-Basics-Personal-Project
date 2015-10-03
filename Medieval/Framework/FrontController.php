<?php

namespace Medieval\Framework;

use Medieval\Framework\Config\FrameworkRoutingConfig;

use Medieval\Framework\Helpers\BindingsResolver;
use Medieval\Framework\Helpers\DirectoryBuilder;
use Medieval\Framework\Routers\Router;

class FrontController {

    private static $_instance = null;

    private $_controller;

    /** @var Routers\Router $_router */
    private $_router;

    /** @var Routers\RequestUriResult $_uriParseResult */
    private $_uriParseResult;

    private function __construct( $router ) {
        $this->_router = $router;
    }

    public function dispatch() {
        if ( !$_GET || !isset( $_GET[ 'uri' ] ) ) {
            header( 'Location: ' . FrameworkRoutingConfig::AUTHORIZED_REDIRECT );
            exit;
        }

        try {
            $this->_uriParseResult = $this->_router->processRequestUri( $_GET[ 'uri' ] );

            $fullControllerName = DirectoryBuilder::getControllerPath(
                $this->_uriParseResult->getAreaName(),
                $this->_uriParseResult->getControllerName()
            );

            $this->initController( $fullControllerName );

            $bindingResult = BindingsResolver::resolveModelBinding(
                $this->_controller, $this->_uriParseResult->getActionName() );
            $this->_uriParseResult->addRequestParam( $bindingResult );

            View::setAreaName( $this->_uriParseResult->getAreaName() );
            View::setControllerName( $this->_uriParseResult->getControllerName() );
            View::setActionName( $this->_uriParseResult->getActionName() );

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

    private function initController( $controllerName ) {
        if ( !isset( $this->_uriParseResult->getAppStructure()
            [ $this->_uriParseResult->getAreaName() ]
            [ $controllerName ]
            [ $this->_uriParseResult->getActionName() ] )
        ) {
            throw new \Exception( 'Invalid controller or method name.' );
        }
        $this->_controller = new $controllerName(
            $this->_uriParseResult->getAreaName(),
            $this->_uriParseResult->getControllerName(),
            $this->_uriParseResult->getActionName(),
            $this->_uriParseResult->getRequestParams()
        );
    }

    /**
     * @param Router $router
     * @return FrontController
     */
    public static function getInstance( Router $router ) {
        if ( self::$_instance == null ) {
            self::$_instance = new self( $router );
        }

        return self::$_instance;
    }
}