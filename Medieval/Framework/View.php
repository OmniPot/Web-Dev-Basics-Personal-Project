<?php

namespace Medieval\Framework;

use Medieval\Config\AppConfig;

class View {

    public static $areaName;
    public static $controllerName;
    public static $actionName;

    const PARAMS_COUNT_MODEL_AND_VIEW = 2;
    const PARAMS_COUNT_MODEL_OR_VIEW = 1;

    public function __construct() {
        $args = func_get_args();

        if ( count( $args ) == self::PARAMS_COUNT_MODEL_AND_VIEW ) {
            $view = $args[ 0 ];
            $model = $args[ 1 ];
            $this->initModelView( $view, $model );
        } else {
            $model = isset( $args[ 0 ] ) ? $args[ 0 ] : null;
            $this->initModelOnly( $model );
        }
    }

    private function initModelOnly( $model ) {
        require_once
            AppConfig::AREAS_NAMESPACE
            . self::$areaName
            . AppConfig::AREA_SUFFIX
            . AppConfig::VIEWS_NAMESPACE
            . self::$controllerName
            . DIRECTORY_SEPARATOR
            . self::$actionName
            . AppConfig::PHP_EXTENSION;
    }

    private function initModelView( $view, $model ) {
        require_once
            AppConfig::AREAS_NAMESPACE
            . self::$areaName
            . AppConfig::AREA_SUFFIX
            . AppConfig::VIEWS_NAMESPACE
            . self::$controllerName
            . DIRECTORY_SEPARATOR
            . self::$actionName
            . AppConfig::PHP_EXTENSION;
    }
}