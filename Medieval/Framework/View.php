<?php

namespace Medieval\Framework;

use Medieval\Config\AppConfig;

class View {

    private static $_areaName;
    private static $_controllerName;
    private static $_actionName;

    const TWO_PARAMS = 2;
    const SINGLE_PARAMS = 1;

    public function __construct( $model = null, $view = null ) {
        if ( $model ) {
            $viewFile = $this->validateModelType( $model, $view );
            if ( $viewFile ) {
                $this->renderView( $viewFile, $model );
            }
        }
    }

    public function getAreaName() {
        return self::$_areaName;
    }

    public static function setAreaName( $_areaName ) {
        self::$_areaName = $_areaName;
    }

    public function getControllerName() {
        return self::$_controllerName;
    }

    public static function setControllerName( $_controllerName ) {
        self::$_controllerName = $_controllerName;
    }

    public function getActionName() {
        return self::$_actionName;
    }

    public static function setActionName( $_actionName ) {
        self::$_actionName = $_actionName;
    }

    private function renderView( $view, $model ) {
        require_once $view;
    }

    private function validateModelType( $model, $viewName = null ) {
        $viewFile = $this->getViewPath( $viewName );
        $viewContent = file_get_contents( $viewFile );

        $typeRegex = '/@var\s*.*\s+(' . AppConfig::VENDOR_NAMESPACE . '\\.*?)\s+\s*.*/';
        preg_match( $typeRegex, $viewContent, $matches );

        if ( !isset( $matches[ 1 ] ) ) {
            throw new \Exception( 'Invalid model type provided' );
        }

        $typeGiven = get_class( $model );
        $typeExpected = $matches[ 1 ];

        if ( $typeGiven != $typeExpected ) {
            throw new \Exception( 'Invalid type supplied' );
        }

        return $viewFile;
    }

    private function getViewPath( $viewName = null ) {
        $view = AppConfig::VIEWS_NAMESPACE
            . $this->getControllerName()
            . DIRECTORY_SEPARATOR
            . ( $viewName ? $viewName : $this->getActionName() )
            . AppConfig::PHP_EXTENSION;

        if ( $this->getAreaName() != AppConfig::DEFAULT_AREA ) {
            $view = AppConfig::AREAS_NAMESPACE
                . $this->getAreaName()
                . AppConfig::AREA_SUFFIX
                . $view;
        }

        if ( !is_file( $view ) || !is_readable( $view ) ) {
            throw new \Exception( 'View not found' );
        }

        return $view;
    }
}