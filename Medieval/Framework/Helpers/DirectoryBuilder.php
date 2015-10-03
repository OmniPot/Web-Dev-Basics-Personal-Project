<?php

namespace Medieval\Framework\Helpers;

use Medieval\Config\AppConfig;

class DirectoryBuilder {

    public static function getControllerPath( $areaName, $controllerName ) {
        if ( !$areaName ) {
            throw new \Exception( 'No area name to get the controller name from' );
        }

        if ( !$controllerName ) {
            throw new \Exception( 'No controller name to process' );
        }

        $fullControllerName = AppConfig::VENDOR_NAMESPACE;
        if ( $areaName != AppConfig::DEFAULT_AREA ) {
            $fullControllerName .= AppConfig::AREAS_NAMESPACE . $areaName . AppConfig::AREA_SUFFIX;
        }

        $fullControllerName .= AppConfig::CONTROLLERS_NAMESPACE . $controllerName . AppConfig::CONTROLLER_SUFFIX;

        return $fullControllerName;
    }

    public static function extractControllerName( $controllerFullPath ) {
        if ( !$controllerFullPath ) {
            throw new \Exception( 'No full name to extract from' );
        }

        $controllerNameRegex =
            '/' . AppConfig::CONTROLLERS_NAMESPACE . '\(.*)' . AppConfig::CONTROLLER_SUFFIX . '/';

        preg_match( $controllerNameRegex, $controllerFullPath, $controllerMatches );
        $controller = lcfirst( $controllerMatches[ 1 ] );

        return $controller;
    }

    public static function getViewPath( $controllerName, $actionName, $areaName = null, $viewName = null ) {
        $view = AppConfig::VIEWS_NAMESPACE
            . $controllerName
            . DIRECTORY_SEPARATOR
            . ( $viewName ? $viewName : $actionName )
            . AppConfig::PHP_EXTENSION;

        if ( $areaName != AppConfig::DEFAULT_AREA ) {
            $view = AppConfig::AREAS_NAMESPACE
                . $areaName
                . AppConfig::AREA_SUFFIX
                . $view;
        }

        if ( !is_file( $view ) || !is_readable( $view ) ) {
            throw new \Exception( 'View not found' );
        }

        return $view;
    }
}