<?php

namespace Medieval\Framework;

use Medieval\Framework\Config\FrameworkRoutingConfig;
use Medieval\Framework\Config\DatabaseConfig;
use Medieval\Framework\Routers\Router;

class App {

    /** @var $_frontController \Medieval\Framework\App */
    private static $_instance = null;

    private function __construct() {

    }

    public function start() {
        $this->initAutoload();

        Database::setInstance(
            DatabaseConfig::DB_INSTANCE_NAME,
            DatabaseConfig::DB_DRIVER,
            DatabaseConfig::DB_USERNAME,
            DatabaseConfig::DB_PASSWORD,
            DatabaseConfig::DB_NAME,
            DatabaseConfig::DB_HOST
        );

        $_router = new Router( FrameworkRoutingConfig::getCustomMappings() );
        $_frontController = FrontController::getInstance( $_router );

        $_frontController->dispatch();
    }

    public function initAutoload() {
        spl_autoload_register( function ( $class ) {
            $classPath = str_replace( [ '\\', '/' ], DIRECTORY_SEPARATOR, $class );
            $classPath = str_replace( 'Medieval\\', '', $class . '.php' );

            if ( file_exists( $classPath ) && is_readable( $classPath ) ) {
                require_once $classPath;
            } else {
                throw new \Exception( 'File not found or is not readable: ' . $classPath );
            }
        } );
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}