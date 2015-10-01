<?php

namespace Medieval\Framework;

use Medieval\Config\AppConfig;
use Medieval\Framework\Config\DatabaseConfig;
use Medieval\Config\BaseRoutingConfig;

class BaseController {

    protected $databaseInstance;

    protected $_areaName;
    protected $_controllerName;
    protected $_actionName;
    protected $_requestParams;

    protected $alreadyAuthorizedLocation = BaseRoutingConfig::AUTHORIZED_REDIRECT;
    protected $unauthorizedLocation = BaseRoutingConfig::UNAUTHORIZED_REDIRECT;

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
        if ( !$location ) {
            throw new \Exception( 'Invalid location' );
        }

        $newUriStart = strpos( $_SERVER[ 'REQUEST_URI' ], $_GET[ 'uri' ] );
        $newUri = str_replace( $_GET[ 'uri' ], $location, $_SERVER[ 'REQUEST_URI' ] );
        $newUri = str_replace( AppConfig::VENDOR_NAMESPACE, '', $newUri );

        header( 'Location: ' . $newUri );
        exit;
    }
}