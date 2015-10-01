<?php

namespace Medieval\Framework\Routers;

use Medieval\Config\AppConfig;
use Medieval\Config\BaseRoutingConfig;
use Medieval\Framework\AnnotationsOperator;

abstract class BaseRouter {

    protected $appStructure = array();

    protected $areaName;
    protected $controllerName;
    protected $actionName;

    protected $requestParams = array();

    protected function __construct() {
        $this->registerAreas( AppConfig::AREAS_NAMESPACE, AppConfig::AREA_SUFFIX );
        $this->registerDefaultAreaControllers();

        foreach ( $this->appStructure as $key => $value ) {
            $this->registerAreaControllers( $key );
        }

        $this->registerControllersActions();

        $this->registerAnnotationRoutes();

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
    public abstract function processDefaultRequestUri( $uri );

    private function registerAreas( $namespace, $suffix ) {
        foreach ( glob( $namespace . '*' . $suffix ) as $areaPath ) {
            if ( file_exists( $areaPath ) && is_readable( $areaPath ) ) {
                $areaName = str_replace( [ $namespace, $suffix ], '', $areaPath );
                $this->appStructure[ $areaName ] = [ ];
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
                $this->appStructure[ AppConfig::DEFAULT_AREA ][ $fullPath ] = [ ];
            }
        }
    }

    private function registerAreaControllers( $areaName ) {
        $path = $this->getAreaFolder( $areaName );

        foreach ( glob( $path ) as $controllerPath ) {
            if ( file_exists( $controllerPath ) && is_readable( $controllerPath ) ) {
                $fullPath = AppConfig::VENDOR_NAMESPACE .
                    str_replace( AppConfig::PHP_EXTENSION, '', $controllerPath );
                $this->appStructure[ $areaName ][ $fullPath ] = [ ];
            } else {
                throw new \Exception( 'File not found or is not readable: ' . $controllerPath );
            }
        }
    }

    private function registerControllersActions() {
        foreach ( $this->appStructure as $area => $areaControllers ) {
            foreach ( $areaControllers as $areaController => $areaMethods ) {
                $methods = array_values( get_class_methods( $areaController ) );
                foreach ( $methods as $method ) {
                    $this->appStructure[ $area ][ $areaController ][ $method ] = true;
                }
            }
        }
    }

    private function registerAnnotationRoutes() {
        foreach ( $this->appStructure as $area => $controllers ) {
            if ( count( array_keys( $controllers ) ) ) {
                foreach ( array_keys( $controllers ) as $controller ) {
                    $class = AnnotationsOperator::getReflectionClass( $controller );
                    $actions = AnnotationsOperator::getClassActions( $class );
                    $actionDocRoutes = AnnotationsOperator::getActionRoutes( $actions );

                    if ( $actionDocRoutes ) {
                        foreach ( $actionDocRoutes as $action => $route ) {
                            $parsedRoute = AnnotationsOperator::parseActionRoute( $route );
                            if ( $parsedRoute ) {
                                $route = $this->getValidRequestRoute( $area, $controller, $action );

                                BaseRoutingConfig::setAnnotationMapping(
                                    $parsedRoute[ 'uri' ], $route, $parsedRoute[ 'params' ] );
                            }
                        }
                    }
                }
            }
        }
    }

    private function getValidRequestRoute( $area, $controllerFullPath, $action ) {

        $controllerNameRegex =
            '/' . AppConfig::CONTROLLERS_NAMESPACE . '\(.*)' . AppConfig::CONTROLLER_SUFFIX . '/';

        preg_match( $controllerNameRegex, $controllerFullPath, $controllerMatches );

        $controller = strtolower( $controllerMatches[ 1 ] );
        $area = strtolower( $area );

        $route = "$area/$controller/$action";

        return $route;
    }

    private function getAreaFolder( $areaName ) {
        return AppConfig::AREAS_NAMESPACE
        . $areaName
        . AppConfig::AREA_SUFFIX
        . AppConfig::CONTROLLERS_NAMESPACE . '*'
        . AppConfig::PHP_EXTENSION;
    }
}