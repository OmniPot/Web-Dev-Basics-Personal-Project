<?php

namespace Medieval\Controllers;

use Medieval\Framework\Config\DatabaseConfig;
use Medieval\Config\RoutingConfig;

use Medieval\Framework\Database;

class BaseController {

    protected $databaseInstance;

    protected $_areaName;
    protected $_controllerName;
    protected $_actionName;
    protected $_requestParams;

    protected $unauthorizedLocation = RoutingConfig::UNAUTHORIZED_REDIRECT;
    protected $alreadyAuthorizedLocation = RoutingConfig::AUTHORIZED_REDIRECT;

    public function __construct( $areaName = null, $controllerName, $actionName, array $requestParams = [ ] ) {
        $this->_areaName = $areaName;
        $this->_controllerName = $controllerName;
        $this->_actionName = $actionName;
        $this->_requestParams = $requestParams;

        $this->databaseInstance = Database::getInstance( DatabaseConfig::DB_INSTANCE_NAME );
    }

    protected function isLogged() {
        return isset( $_SESSION[ 'id' ] );
    }

    protected function redirect( $location ) {
        $urlStart = $this->_areaName ?
            stripos( $_SERVER[ 'REQUEST_URI' ], $this->_areaName ) :
            stripos( $_SERVER[ 'REQUEST_URI' ], $this->_controllerName );

        $resultUri = substr( $_SERVER[ 'REQUEST_URI' ], 0, $urlStart ) . $location;
        header( 'Location: ' . $resultUri );
        exit;
    }
}