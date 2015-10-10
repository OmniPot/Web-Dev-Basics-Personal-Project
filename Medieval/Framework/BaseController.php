<?php

namespace Medieval\Framework;

use Medieval\Config\RoutingConfig;

use Medieval\Framework\Config\FrameworkConfig;
use Medieval\Framework\Config\DatabaseConfig;
use Medieval\Framework\Database\Database;

class BaseController {

    protected $databaseInstance;

    protected $_areaName;
    protected $_controllerName;
    protected $_actionName;
    protected $_requestParams;

    protected $alreadyAuthorizedLocation = RoutingConfig::AUTHORIZED_REDIRECT;
    protected $unauthorizedLocation = RoutingConfig::UNAUTHORIZED_REDIRECT;

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

        $fullUri = $_SERVER[ 'REQUEST_URI' ];
        $customUri = $_GET[ 'uri' ];

        $newUri = str_replace( $customUri, $location, $fullUri );
        $newUri = str_replace( FrameworkConfig::VENDOR_NAMESPACE, '', $newUri );

        header( 'Location: ' . $newUri );
        exit;
    }
}