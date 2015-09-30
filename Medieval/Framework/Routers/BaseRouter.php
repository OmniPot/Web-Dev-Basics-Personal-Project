<?php

namespace Medieval\Framework\Routers;

use Medieval\Config\AppConfig;

abstract class BaseRouter {

    protected $areas = array();

    protected $areaName;
    protected $controllerName;
    protected $actionName;

    protected $requestParams = array();

    protected function __construct() {
        $this->registerAreas( AppConfig::AREAS_NAMESPACE, AppConfig::AREA_SUFFIX );
        $this->registerDefaultAreaControllers();

        foreach ( $this->areas as $key => $value ) {
            $this->registerAreaControllers( $key );
        }

        $this->registerControllersActions();

        $this->setAreaName( AppConfig::DEFAULT_AREA );
        $this->setControllerName( AppConfig::DEFAULT_CONTROLLER );
        $this->setActionName( AppConfig::DEFAULT_ACTION );
    }

    public function getAreas() {
        return $this->areas;
    }

    protected function setAreas( $areas ) {
        $this->areas = $areas;
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

    private function getAreaFolder( $areaName ) {
        return AppConfig::AREAS_NAMESPACE
        . $areaName
        . AppConfig::AREA_SUFFIX
        . AppConfig::CONTROLLERS_NAMESPACE . '*'
        . AppConfig::PHP_EXTENSION;
    }

    /**
     * @param $uri string
     * @return RequestUriResult $result
     */
    public abstract function processRequestUri( $uri );

    protected function registerDefaultAreaControllers() {
        $this->areas[ ucfirst( AppConfig::DEFAULT_AREA ) ] = [ ];
        $globParam = AppConfig::CONTROLLERS_NAMESPACE . '*' . AppConfig::PHP_EXTENSION;

        foreach ( glob( $globParam ) as $controllerPath ) {
            if ( file_exists( $controllerPath ) && is_readable( $controllerPath ) ) {
                $fullPath = AppConfig::VENDOR_NAMESPACE . str_replace( AppConfig::PHP_EXTENSION, '', $controllerPath );
                $this->areas[ AppConfig::DEFAULT_AREA ][ $fullPath ] = [ ];
            }
        }
    }

    private function registerAreas( $namespace, $suffix ) {
        foreach ( glob( $namespace . '*' . $suffix ) as $areaPath ) {
            if ( file_exists( $areaPath ) && is_readable( $areaPath ) ) {
                $areaName = str_replace( [ $namespace, $suffix ], '', $areaPath );
                $this->areas[ $areaName ] = [ ];
            } else {
                throw new \Exception( 'Directory not found: ' . $areaPath );
            }
        }
    }

    private function registerAreaControllers( $areaName ) {
        $path = $this->getAreaFolder( $areaName );

        foreach ( glob( $path ) as $controllerPath ) {
            if ( file_exists( $controllerPath ) && is_readable( $controllerPath ) ) {
                $fullPath = AppConfig::VENDOR_NAMESPACE .
                    str_replace( AppConfig::PHP_EXTENSION, '', $controllerPath );
                $this->areas[ $areaName ][ $fullPath ] = [ ];
            } else {
                throw new \Exception( 'File not found or is not readable: ' . $controllerPath );
            }
        }
    }

    private function registerControllersActions() {
        foreach ( $this->areas as $area => $areaControllers ) {
            foreach ( $areaControllers as $areaController => $areaMethods ) {
                $methods = array_values( get_class_methods( $areaController ) );
                foreach ( $methods as $method ) {
                    $this->areas[ $area ][ $areaController ][ $method ] = true;
                }
            }
        }
    }
}