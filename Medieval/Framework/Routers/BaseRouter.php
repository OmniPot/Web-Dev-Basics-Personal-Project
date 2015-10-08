<?php

namespace Medieval\Framework\Routers;

use Medieval\Config\AppConfig;
use Medieval\Framework\Config\FrameworkConfig;
use Medieval\Framework\Helpers\AnnotationParser;
use Medieval\Framework\Helpers\DirectoryHelper;
use Medieval\Framework\Helpers\FileHelper;

abstract class BaseRouter {

    protected $_appStructure = array();
    protected $_actionsArray = array();

    protected $_requestMethod;
    protected $_userRole;

    private $_areaName;
    private $_controllerName;
    private $_actionName;

    protected $requestParams = array();

    protected function __construct() {
        $this->_requestMethod = $_SERVER[ 'REQUEST_METHOD' ];
        $this->_userRole = 'guest';

        if ( isset( $_SESSION[ 'role' ] ) ) {
            $this->_userRole = $_SESSION[ 'role' ];
        }

        $this->setupAppStructureConfig();

        $this->setAreaName( FrameworkConfig::DEFAULT_AREA );
        $this->setControllerName( FrameworkConfig::DEFAULT_CONTROLLER );
        $this->setActionName( FrameworkConfig::DEFAULT_ACTION );
    }

    public function getAppStructure() {
        return $this->_appStructure;
    }

    protected function setAppStructure( $_appStructure ) {
        $this->_appStructure = $_appStructure;
    }

    public function getActionsArray() {
        return $this->_actionsArray;
    }

    public function setActionsArray( $actionsArray ) {
        $this->_actionsArray = $actionsArray;
    }

    public function getAreaName() {
        return $this->_areaName;
    }

    protected function setAreaName( $_areaName ) {
        $this->_areaName = $_areaName;
    }

    public function getControllerName() {
        return $this->_controllerName;
    }

    protected function setControllerName( $_controllerName ) {
        $this->_controllerName = $_controllerName;
    }

    public function getActionName() {
        return $this->_actionName;
    }

    protected function setActionName( $_actionName ) {
        $this->_actionName = $_actionName;
    }

    public function getRequestParams() {
        return $this->requestParams;
    }

    protected function setRequestParams( $requestParams ) {
        $this->requestParams = $requestParams;
    }

    /**
     * @param $uri string
     * @return RequestUriResult $result
     */
    public abstract function processRequestUri( $uri );

    private function setupAppStructureConfig() {
        if ( !file_exists( FrameworkConfig::APP_STRUCTURE_NAME ) ||
            !is_readable( FrameworkConfig::APP_STRUCTURE_NAME )
        ) {
            $this->writeAppStructureConfig();
        } else {
            include_once FrameworkConfig::APP_STRUCTURE_NAME;

            if ( empty( $expires ) && empty( $appStructure ) && empty( $actionsStructure ) ) {
                throw new \Exception( 'App structure config contains invalid information' );
            }

            $now = new \DateTime( 'now', new \DateTimeZone( AppConfig::TIME_ZONE ) );
            $expires = new \DateTime( $expires, new \DateTimeZone( AppConfig::TIME_ZONE ) );

            if ( $now->getTimestamp() > $expires->getTimestamp() ) {
                unlink( FrameworkConfig::APP_STRUCTURE_NAME );
                $this->writeAppStructureConfig();
            }

            $this->setAppStructure( $appStructure );
            $this->setActionsArray( $actionsStructure );
        }
    }

    private function writeAppStructureConfig() {
        $this->registerAppStructure();

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
            throw new \Exception( "Controller: $fullControllerName not found in area: " . $this->getAreaName() );
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
}