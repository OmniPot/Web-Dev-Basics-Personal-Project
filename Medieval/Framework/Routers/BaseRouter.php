<?php

namespace Medieval\Framework\Routers;

use Medieval\Config\AppConfig;
use Medieval\Framework\Helpers\AnnotationParser;
use Medieval\Framework\Helpers\DirectoryBuilder;

abstract class BaseRouter {

    protected $appStructure = array();

    protected $areaName;
    protected $controllerName;
    protected $actionName;

    protected $requestParams = array();

    protected function __construct() {
        $this->registerAppStructure();

        $this->setAreaName( AppConfig::DEFAULT_AREA );
        $this->setControllerName( AppConfig::DEFAULT_CONTROLLER );
        $this->setActionName( AppConfig::DEFAULT_ACTION );
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

    protected function validateRequestMethod() {
        $requestMethod = $_SERVER[ 'REQUEST_METHOD' ];
        $routeFound = null;

        foreach ( $this->appStructure as $areaKey => $controllers ) {
            foreach ( $controllers as $controllerKey => $actions ) {
                foreach ( $actions as $actionKey => $actionValues ) {
                    if ( $actionKey == $this->getActionName() ) {
                        if ( $requestMethod == $actionValues[ 'method' ] ) {
                            $routeFound = true;
                        }
                    }
                }
            }
        }

        if ( !$routeFound ) {
            throw new \Exception( 'No route found that matches the request uri and method' );
        }
    }

    private function registerAppStructure() {
        foreach ( glob( AppConfig::AREAS_NAMESPACE . '*' . AppConfig::AREA_SUFFIX ) as $areaPath ) {
            if ( file_exists( $areaPath ) && is_readable( $areaPath ) ) {
                $areaName = str_replace( [ AppConfig::AREAS_NAMESPACE, AppConfig::AREA_SUFFIX ], '', $areaPath );
                $this->appStructure[ $areaName ] = array();

                $this->registerDefaultAreaControllers();
                $this->registerAreaControllers( $areaPath, $areaName );
            } else {
                throw new \Exception( 'Directory not found: ' . $areaPath );
            }
        }
    }

    private function registerDefaultAreaControllers() {
        $this->appStructure[ ucfirst( AppConfig::DEFAULT_AREA ) ] = [ ];
        $globParam = AppConfig::CONTROLLERS_NAMESPACE . '*' . AppConfig::PHP_EXTENSION;

        foreach ( glob( $globParam ) as $controllerPath ) {
            if ( file_exists( $controllerPath ) && is_readable( $controllerPath ) ) {
                $fullPath = AppConfig::VENDOR_NAMESPACE . str_replace( AppConfig::PHP_EXTENSION, '', $controllerPath );
                $this->appStructure[ AppConfig::DEFAULT_AREA ][ $fullPath ] = array();
            }
        }
    }

    private function registerAreaControllers( $areaPath, $areaName ) {
        foreach ( glob( $areaPath . AppConfig::CONTROLLERS_NAMESPACE . '*' . AppConfig::PHP_EXTENSION ) as $controllerPath ) {
            if ( file_exists( $controllerPath ) && is_readable( $controllerPath ) ) {
                $fullPath = AppConfig::VENDOR_NAMESPACE . str_replace( AppConfig::PHP_EXTENSION, '', $controllerPath );
                $this->appStructure[ $areaName ][ $fullPath ] = array();

                $this->registerControllersActions( $areaName, $fullPath );
                $this->registerControllersActions( AppConfig::DEFAULT_AREA,
                    AppConfig::VENDOR_NAMESPACE
                    . AppConfig::CONTROLLERS_NAMESPACE
                    . AppConfig::DEFAULT_CONTROLLER
                    . AppConfig::CONTROLLER_SUFFIX
                );

            } else {
                throw new \Exception( 'File not found or is not readable: ' . $controllerPath );
            }
        }
    }

    private function registerControllersActions( $areaName, $fullPath ) {
        $class = new \ReflectionClass( $fullPath );
        $actions = $class->getMethods();
        $actionDocs = AnnotationParser::getActionRoutes( $actions );

        if ( $actionDocs ) {
            foreach ( $actionDocs as $action => $doc ) {
                $parsedDocsArray = AnnotationParser::parseActionDoc( $doc );

                if ( $parsedDocsArray ) {
                    $this->appStructure[ $areaName ][ $fullPath ][ $action ] = [ ];
                    $realRoute = $this->getValidRouteUri( $areaName, $fullPath, $action );
                    $parsedDocsArray[ 'defaultRoute' ] = $realRoute;
                    $this->appStructure[ $areaName ][ $fullPath ][ $action ] = $parsedDocsArray;
                }
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