<?php

namespace Medieval\Framework;

use Medieval\Framework\Config\FrameworkRoutingConfig;

use Medieval\Framework\Helpers\BindingHelper;
use Medieval\Framework\Helpers\DirectoryHelper;
use Medieval\Framework\Routers\Router;

class FrontController {

    private static $_instance = null;

    /** @var BaseController $_controller */
    private $_controller;

    /** @var Routers\Router $_router */
    private $_router;

    /** @var Routers\RequestUriResult $_uriParsedResult */
    private $_uriParsedResult;

    private function __construct( $router ) {
        $this->_router = $router;
    }

    public function dispatch() {
        if ( !$_GET || !isset( $_GET[ 'uri' ] ) ) {
            header( 'Location: ' . FrameworkRoutingConfig::AUTHORIZED_REDIRECT );
            exit;
        }

        try {
            $this->_uriParsedResult = $this->_router->processRequestUri( $_GET[ 'uri' ] );

            $this->initController();

            $bindingResult = BindingHelper::resolveModelBinding(
                $this->_controller,
                $this->_uriParsedResult->getActionName()
            );

            $this->_uriParsedResult->addRequestParam( $bindingResult );

            View::setAreaName( $this->_uriParsedResult->getAreaName() );
            View::setControllerName( $this->_uriParsedResult->getControllerName() );
            View::setActionName( $this->_uriParsedResult->getActionName() );

            call_user_func_array(
                [
                    $this->_controller,
                    $this->_uriParsedResult->getActionName()
                ],
                $this->_uriParsedResult->getRequestParams() );

        } catch ( \Exception $exception ) {
            echo $exception->getMessage();
        }
    }

    private function initController() {
        $fullControllerName = DirectoryHelper::getControllerPath(
            $this->_uriParsedResult->getAreaName(),
            $this->_uriParsedResult->getControllerName()
        );

        if ( !isset( $this->_uriParsedResult->getAppStructure()
            [ $this->_uriParsedResult->getAreaName() ]
            [ $fullControllerName ]
            [ $this->_uriParsedResult->getActionName() ] )
        ) {
            throw new \Exception( 'Invalid controller or method name.' );
        }
        $this->_controller = new $fullControllerName(
            $this->_uriParsedResult->getAreaName(),
            $this->_uriParsedResult->getControllerName(),
            $this->_uriParsedResult->getActionName(),
            $this->_uriParsedResult->getRequestParams()
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