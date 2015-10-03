<?php

namespace Medieval\Framework\Routers;

use Medieval\Framework\Config\FrameworkConfig;
use Medieval\Framework\Helpers\AnnotationParser;
use Medieval\Framework\Helpers\DirectoryBuilder;

abstract class BaseRouter {

    protected $appStructure = array();
    protected $_actionsArray = array();

    protected $_requestMethod;
    protected $_userRole;

    protected $areaName;
    protected $controllerName;
    protected $actionName;

    protected $requestParams = array();

    protected function __construct() {
        $this->_requestMethod = $_SERVER[ 'REQUEST_METHOD' ];
        $this->_userRole = isset( $_SESSION[ 'role' ] ) ? $_SESSION[ 'role' ] : 'guest';

        $this->registerAppStructure();

        $this->setAreaName( FrameworkConfig::DEFAULT_AREA );
        $this->setControllerName( FrameworkConfig::DEFAULT_CONTROLLER );
        $this->setActionName( FrameworkConfig::DEFAULT_ACTION );
    }

    public function getAppStructure() {
        return $this->appStructure;
    }

    protected function setAppStructure( $appStructure ) {
        $this->appStructure = $appStructure;
    }

    public function getAreaName() {
        return $this->areaName;
    }

    protected function setAreaName( $areaName ) {
        $this->areaName = $areaName;
    }

    public function getControllerName() {
        return $this->controllerName;
    }

    protected function setControllerName( $controllerName ) {
        $this->controllerName = $controllerName;
    }

    public function getActionName() {
        return $this->actionName;
    }

    protected function setActionName( $actionName ) {
        $this->actionName = $actionName;
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

    private function registerAppStructure() {
        foreach ( glob( FrameworkConfig::AREAS_NAMESPACE . '*' . FrameworkConfig::AREA_SUFFIX ) as $areaPath ) {
            if ( file_exists( $areaPath ) && is_readable( $areaPath ) ) {
                $areaName = str_replace( [ FrameworkConfig::AREAS_NAMESPACE, FrameworkConfig::AREA_SUFFIX ], '', $areaPath );
                $this->appStructure[ $areaName ] = [ ];

                $this->registerDefaultAreaControllers();
                $this->registerAreaControllers( $areaPath, $areaName );
            } else {
                throw new \Exception( 'Directory not found: ' . $areaPath );
            }
        }
    }

    private function registerDefaultAreaControllers() {
        $this->appStructure[ ucfirst( FrameworkConfig::DEFAULT_AREA ) ] = [ ];
        $globParam = FrameworkConfig::CONTROLLERS_NAMESPACE . '*' . FrameworkConfig::PHP_EXTENSION;

        foreach ( glob( $globParam ) as $controllerPath ) {
            if ( file_exists( $controllerPath ) && is_readable( $controllerPath ) ) {
                $fullPath = FrameworkConfig::VENDOR_NAMESPACE . str_replace( FrameworkConfig::PHP_EXTENSION, '', $controllerPath );
                $this->appStructure[ FrameworkConfig::DEFAULT_AREA ][ $fullPath ] = [ ];
            }
        }
    }

    private function registerAreaControllers( $areaPath, $areaName ) {
        foreach ( glob( $areaPath . FrameworkConfig::CONTROLLERS_NAMESPACE . '*' . FrameworkConfig::PHP_EXTENSION ) as $controllerPath ) {
            if ( file_exists( $controllerPath ) && is_readable( $controllerPath ) ) {
                $fullPath = FrameworkConfig::VENDOR_NAMESPACE . str_replace( FrameworkConfig::PHP_EXTENSION, '', $controllerPath );
                $this->appStructure[ $areaName ][ $fullPath ] = [ ];

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

            $this->appStructure[ $areaName ][ $fullPath ][ $action->name ] = [ ];
            $realRoute = $this->getValidRouteUri( $areaName, $fullPath, $action->name );

            $actionDoc = AnnotationParser::getActionDoc( $action );

            if ( $actionDoc ) {
                $parsedDocsArray = AnnotationParser::parseActionDoc( $actionDoc );
                if ( $parsedDocsArray ) {
                    $parsedDocsArray[ 'defaultRoute' ] = $realRoute;
                    $this->appStructure[ $areaName ][ $fullPath ][ $action->name ] = $parsedDocsArray;
                    $this->_actionsArray[ $action->name ] = $this->appStructure[ $areaName ][ $fullPath ][ $action->name ];
                }
            } else {
                $this->appStructure[ $areaName ][ $fullPath ][ $action->name ] = [ ];
                $this->_actionsArray[ $action->name ] = $this->appStructure[ $areaName ][ $fullPath ][ $action->name ];
            }
        }
    }

    private function getValidRouteUri( $areaName, $fullControllerName, $actionName ) {
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

        $controller = DirectoryBuilder::extractControllerName( $fullControllerName );
        $area = strtolower( $areaName );
        $route = "$area/$controller/$actionName";

        return $route;
    }
}