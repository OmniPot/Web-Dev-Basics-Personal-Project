<?php

namespace Medieval\Framework;

use Medieval\Application\Config\DatabaseConfig;

class App {

    /** @var $_frontController \Medieval\Framework\App */
    private static $_instance = null;

    /** @var $_frontController \Medieval\Framework\FrontController */
    private $_frontController = null;

    private function __construct() {
        $this->initAutoload();
        $this->_frontController = FrontController::getInstance();
    }

    public function start() {

        Database::setInstance(
            DatabaseConfig::DB_INSTANCE_NAME,
            DatabaseConfig::DB_DRIVER,
            DatabaseConfig::DB_USERNAME,
            DatabaseConfig::DB_PASSWORD,
            DatabaseConfig::DB_NAME,
            DatabaseConfig::DB_HOST
        );

        $this->_frontController->dispatch();
    }

    private function initAutoload() {
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