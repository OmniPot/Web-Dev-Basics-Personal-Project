<?php

namespace Medieval\Application\TestArea\Controllers;

use Medieval\Framework\Config\DatabaseConfig;
use Medieval\Application\Config\RoutingConfig;

use Medieval\Framework\Database;

class BaseController {

    protected $databaseInstance;

    protected $fullControllerName;
    protected $customControllerName;
    protected $requestParams;

    protected $unauthorizedLocation = RoutingConfig::UNAUTHORIZED_REDIRECT;
    protected $alreadyAuthorizedLocation = RoutingConfig::AUTHORIZED_REDIRECT;

    public function __construct( $fullControllerName, $customControllerName, array $requestParams = [ ] ) {
        $this->fullControllerName = $fullControllerName;
        $this->customControllerName = $customControllerName;
        $this->requestParams = $requestParams;

        $this->databaseInstance = Database::getInstance( DatabaseConfig::DB_INSTANCE_NAME );
    }

    protected function isLogged() {
        return isset( $_SESSION[ 'id' ] );
    }

    protected function redirect( $location ) {
        $controllerPos = stripos( $_SERVER[ 'REQUEST_URI' ], $this->customControllerName );
        $resultUri = substr( $_SERVER[ 'REQUEST_URI' ], 0, $controllerPos ) . $location;
        header( 'Location: ' . $resultUri );
        exit;
    }
}