<?php

namespace Medieval\Framework;

use Medieval\Framework\Config\FrameworkConfig;
use Medieval\Framework\Helpers\DirectoryBuilder;

class View {

    private static $_areaName;
    private static $_controllerName;
    private static $_actionName;

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
        $viewFile = DirectoryBuilder::getViewPath(
            $this->getControllerName(),
            $this->getActionName(),
            $this->getAreaName(),
            $viewName );

        $viewContent = file_get_contents( $viewFile );

        $typeRegex = '/@var\s*.*\s+(' . FrameworkConfig::VENDOR_NAMESPACE . '\\.*?)\s+\s*.*/';
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
}