<?php

namespace Medieval\Controllers;

use Medieval\Config\RoutingConfig;
use Medieval\Framework\Config\FrameworkConfig;
use Medieval\Framework\Config\DatabaseConfig;

use Medieval\Framework\Database\Database;
use Medieval\Framework\Routers\RequestUriResult;
use Medieval\Framework\View;

class BaseController {

    /** @var Database $_databaseInstance */
    protected $_databaseInstance;

    /** @var  View $_view */
    protected $_view;

    protected $_areaName;
    protected $_controllerName;
    protected $_actionName;
    protected $_requestParams;

    protected $alreadyAuthorizedLocation = RoutingConfig::AUTHORIZED_REDIRECT;
    protected $unauthorizedLocation = RoutingConfig::UNAUTHORIZED_REDIRECT;

    public function __construct( RequestUriResult $requestParseResult, $view ) {
        $this->_areaName = $requestParseResult->getAreaName();
        $this->_controllerName = $requestParseResult->getControllerName();
        $this->_actionName = $requestParseResult->getActionName();

        $this->_requestParams = $requestParseResult->getRequestParams();
        $this->_view = $view;

        $this->_databaseInstance = Database::getInstance( DatabaseConfig::DB_INSTANCE_NAME );
    }

    protected function isLogged() {
        return isset( $_SESSION[ 'id' ] );
    }

    protected function redirect( $location ) {
        if ( !$location ) {
            throw new \Exception( 'Invalid location' );
        }

        $fullUri = $_SERVER[ 'REQUEST_URI' ];
        $customUri = $_GET[ 'uri' ];

        $newUri = str_replace( $customUri, $location, $fullUri );
        $newUri = str_replace( FrameworkConfig::VENDOR_NAMESPACE, '', $newUri );

        header( 'Location: ' . $newUri );
        exit;
    }
}