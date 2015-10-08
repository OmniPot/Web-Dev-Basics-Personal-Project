<?php

namespace Medieval\Framework\Config;

use Medieval\Config\AppConfig;
use Medieval\Framework\Helpers\AnnotationParser;
use Medieval\Framework\Helpers\DirectoryHelper;
use Medieval\Framework\Helpers\FileHelper;

class AppStructureConfig {

    private static $_instance;

    private $_appStructure = array();
    private $_actionsArray = array();

    private function __construct() {

    }

    public function getAppStructure() {
        return $this->_appStructure;
    }

    private function setAppStructure( $_appStructure ) {
        $this->_appStructure = $_appStructure;
    }

    public function getActionsArray() {
        return $this->_actionsArray;
    }

    private function setActionsArray( $_actionsArray ) {
        $this->_actionsArray = $_actionsArray;
    }

    public function setupConfig() {
        if ( !file_exists( FrameworkConfig::APP_STRUCTURE_NAME ) ||
            !is_readable( FrameworkConfig::APP_STRUCTURE_NAME )
        ) {
            $this->writeConfig();
        } else {
            include_once FrameworkConfig::APP_STRUCTURE_NAME;

            if ( empty( $expires ) && empty( $appStructure ) && empty( $actionsStructure ) ) {
                throw new \Exception( 'App structure config contains invalid information' );
            }

            $now = new \DateTime( 'now', new \DateTimeZone( AppConfig::TIME_ZONE ) );
            $expires = new \DateTime( $expires, new \DateTimeZone( AppConfig::TIME_ZONE ) );

            if ( $now->getTimestamp() > $expires->getTimestamp() ) {
                unlink( FrameworkConfig::APP_STRUCTURE_NAME );
                self::writeConfig();
            }

            self::setAppStructure( $appStructure );
            self::setActionsArray( $actionsStructure );
        }
    }

    private function writeConfig() {
        self::registerAppStructure();

        $content = FileHelper::writeFile( $this->_appStructure, $this->_actionsArray );
        file_put_contents( FrameworkConfig::APP_STRUCTURE_NAME, $content );
    }

    private function registerAppStructure() {
        foreach ( glob( FrameworkConfig::AREAS_NAMESPACE . '*' . FrameworkConfig::AREA_SUFFIX ) as $areaPath ) {
            if ( file_exists( $areaPath ) && is_readable( $areaPath ) ) {
                $areaName = str_replace( [ FrameworkConfig::AREAS_NAMESPACE, FrameworkConfig::AREA_SUFFIX ], '', $areaPath );
                $this->_appStructure[ $areaName ] = [ ];

                $this->registerDefaultAreaControllers();
                $this->registerAreaControllers( $areaPath, $areaName );
            } else {
                throw new \Exception( 'Directory not found: ' . $areaPath );
            }
        }
    }

    private function registerDefaultAreaControllers() {
        $this->_appStructure[ ucfirst( FrameworkConfig::DEFAULT_AREA ) ] = [ ];
        $globParam = FrameworkConfig::CONTROLLERS_NAMESPACE . '*' . FrameworkConfig::PHP_EXTENSION;

        foreach ( glob( $globParam ) as $controllerPath ) {
            if ( file_exists( $controllerPath ) && is_readable( $controllerPath ) ) {
                $fullPath = FrameworkConfig::VENDOR_NAMESPACE . str_replace( FrameworkConfig::PHP_EXTENSION, '', $controllerPath );
                $this->_appStructure[ FrameworkConfig::DEFAULT_AREA ][ $fullPath ] = [ ];
            }
        }
    }

    private function registerAreaControllers( $areaPath, $areaName ) {
        foreach ( glob( $areaPath . FrameworkConfig::CONTROLLERS_NAMESPACE . '*' . FrameworkConfig::PHP_EXTENSION ) as $controllerPath ) {
            if ( file_exists( $controllerPath ) && is_readable( $controllerPath ) ) {
                $fullPath = FrameworkConfig::VENDOR_NAMESPACE . str_replace( FrameworkConfig::PHP_EXTENSION, '', $controllerPath );
                $this->_appStructure[ $areaName ][ $fullPath ] = [ ];

                $this->registerControllersActions( $areaName, $fullPath );
                $this->registerControllersActions( FrameworkConfig::DEFAULT_AREA,
                    FrameworkConfig::VENDOR_NAMESPACE
                    . FrameworkConfig::CONTROLLERS_NAMESPACE
                    . FrameworkConfig::DEFAULT_CONTROLLER
                    . FrameworkConfig::CONTROLLER_SUFFIX
                );

            } else {
                throw new \Exception( 'File not found or is not readable: ' . $controllerPath );
            }
        }
    }

    private function registerControllersActions( $areaName, $fullPath ) {
        $class = new \ReflectionClass( $fullPath );
        $actions = $class->getMethods();

        foreach ( $actions as $action ) {
            if ( !$action->isPublic() ) {
                continue;
            }

            $this->_appStructure[ $areaName ][ $fullPath ][ $action->name ] = [ ];
            $realRoute = $this->validateRouteUri( $areaName, $fullPath, $action->name );

            $actionDoc = AnnotationParser::getActionDoc( $action );

            if ( $actionDoc ) {
                $parsedDocsArray = AnnotationParser::parseActionDoc( $actionDoc );
                if ( $parsedDocsArray ) {
                    $parsedDocsArray[ 'defaultRoute' ] = $realRoute;
                    $this->_appStructure[ $areaName ][ $fullPath ][ $action->name ] = $parsedDocsArray;
                    $this->_actionsArray[ $action->name ] = $this->_appStructure[ $areaName ][ $fullPath ][ $action->name ];
                }
            } else {
                $this->_appStructure[ $areaName ][ $fullPath ][ $action->name ] = [ ];
                $this->_actionsArray[ $action->name ] = $this->_appStructure[ $areaName ][ $fullPath ][ $action->name ];
            }
        }
    }

    private function validateRouteUri( $areaName, $fullControllerName, $actionName ) {
        if ( !isset( $this->getAppStructure()[ $areaName ] ) ) {
            throw new \Exception( "Area: $areaName not found." );
        }

        if ( !isset( $this->getAppStructure()[ $areaName ][ $fullControllerName ] ) ) {
            throw new \Exception( "Controller: $fullControllerName not found" );
        }

        if ( !isset( $this->getAppStructure()[ $areaName ][ $fullControllerName ][ $actionName ] ) ) {
            throw new \Exception(
                "Controller: $fullControllerName contains no method: $actionName" );
        }

        $controller = DirectoryHelper::getControllerName( $fullControllerName );
        $area = strtolower( $areaName );
        $route = "$area/$controller/$actionName";

        return $route;
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}