<?php

namespace Medieval\Framework;

use Medieval\Config\RoutingConfig;
use Medieval\Framework\Helpers\DirectoryHelper;
use Medieval\Framework\Routers\RequestUriResult;
use Medieval\Framework\Routers\Router;

class FrontController {

    private static $_instance = null;

    private $_requestUri;
    private $_requestMethod;
    private $_userRole;
    private $_postData;

    /** @var BaseController $_controller */
    private $_controller;

    /** @var Routers\Router $_router */
    private $_router;

    /** @var Routers\RequestUriResult $_uriParsedResult */
    private $_uriParsedResult;

    private function __construct( $router ) {
        if ( !$_GET || !isset( $_GET[ 'uri' ] ) ) {
            header( 'Location: ' . RoutingConfig::AUTHORIZED_REDIRECT );
            exit;
        }

        $this->setRouter( $router );
        $this->setRequestUri( $_GET[ 'uri' ] );
        $this->setRequestMethod( $_SERVER[ 'REQUEST_METHOD' ] );
        $this->setUserRole( isset( $_SESSION[ 'role' ] ) ? $_SESSION[ 'role' ] : 'guest' );
        $this->setPostData( isset( $_POST ) ? $_POST : [ ] );
    }

    public function getRequestUri() {
        return $this->_requestUri;
    }

    private function setRequestUri( $requestUri ) {
        $this->_requestUri = $requestUri;
    }

    public function getRequestMethod() {
        return $this->_requestMethod;
    }

    private function setRequestMethod( $requestMethod ) {
        $this->_requestMethod = $requestMethod;
    }

    public function getUserRole() {
        return $this->_userRole;
    }

    private function setUserRole( $userRole ) {
        $this->_userRole = $userRole;
    }

    public function getController() {
        return $this->_controller;
    }

    private function setController( $controller ) {
        $this->_controller = $controller;
    }

    public function getRouter() {
        return $this->_router;
    }

    private function setRouter( $router ) {
        $this->_router = $router;
    }

    public function getUriParsedResult() {
        return $this->_uriParsedResult;
    }

    private function setUriParsedResult( $uriParsedResult ) {
        $this->_uriParsedResult = $uriParsedResult;
    }

    public function getPostData() {
        return $this->_postData;
    }

    private function setPostData( $postData ) {
        $this->_postData = $postData;
    }

    public function dispatch() {

        try {
            $this->setUriParsedResult(
                $this->getRouter()->processRequestUri(
                    $this->getRequestUri(),
                    $this->getRequestMethod(),
                    $this->getUserRole(),
                    $this->getPostData()
                )
            );

            $this->initController( $this->getUriParsedResult() );

            View::setAreaName( $this->getUriParsedResult()->getAreaName() );
            View::setControllerName( $this->getUriParsedResult()->getControllerName() );
            View::setActionName( $this->getUriParsedResult()->getActionName() );

            call_user_func_array(
                [
                    $this->getController(),
                    $this->getUriParsedResult()->getActionName()
                ],
                $this->getUriParsedResult()->getRequestParams() );

        } catch ( \Exception $exception ) {
            echo $exception->getMessage();
        }
    }

    /**
     * @param RequestUriResult $requestUriResult
     * @throws \Exception
     */
    private function initController( $requestUriResult ) {
        if ( !$requestUriResult ) {
            throw new \Exception( 'Url parse error' );
        }

        $fullControllerName = DirectoryHelper::getControllerPath(
            $requestUriResult->getAreaName(),
            $requestUriResult->getControllerName()
        );

        $controller = new $fullControllerName(
            $requestUriResult->getAreaName(),
            $requestUriResult->getControllerName(),
            $requestUriResult->getActionName(),
            $requestUriResult->getRequestParams()
        );

        $this->setController( $controller );
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