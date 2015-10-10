<?php

namespace Medieval\Framework\Helpers;

use Medieval\Framework\Config\FrameworkConfig;

class DirectoryHelper {

    public static function getControllerPath( $areaName, $controllerName ) {
        if ( !$areaName ) {
            throw new \Exception( 'Invalid area name' );
        }

        if ( !$controllerName ) {
            throw new \Exception( 'Invalid controller name' );
        }

        $fullControllerName = FrameworkConfig::VENDOR_NAMESPACE;
        if ( $areaName != FrameworkConfig::DEFAULT_AREA ) {
            $fullControllerName .= FrameworkConfig::AREAS_NAMESPACE . $areaName . FrameworkConfig::AREA_SUFFIX;
        }

        $fullControllerName .= FrameworkConfig::CONTROLLERS_NAMESPACE . $controllerName . FrameworkConfig::CONTROLLER_SUFFIX;

        return $fullControllerName;
    }

    public static function getControllerName( $controllerFullPath ) {
        if ( !$controllerFullPath ) {
            throw new \Exception( 'Invalid full controller name' );
        }

        $controllerNameRegex =
            '/' . FrameworkConfig::CONTROLLERS_NAMESPACE . '\(.*)' . FrameworkConfig::CONTROLLER_SUFFIX . '/';

        preg_match( $controllerNameRegex, $controllerFullPath, $controllerMatches );
        $controller = lcfirst( $controllerMatches[ 1 ] );

        return $controller;
    }

    public static function getViewPath( $controllerName, $actionName, $areaName = null, $viewName = null ) {
        $view = FrameworkConfig::VIEWS_NAMESPACE
            . $controllerName
            . DIRECTORY_SEPARATOR
            . ( $viewName ? $viewName : $actionName )
            . FrameworkConfig::PHP_EXTENSION;

        if ( $areaName != FrameworkConfig::DEFAULT_AREA ) {
            $view = FrameworkConfig::AREAS_NAMESPACE
                . $areaName
                . FrameworkConfig::AREA_SUFFIX
                . $view;
        }

        if ( !is_file( $view ) || !is_readable( $view ) ) {
            throw new \Exception( 'View not found' );
        }

        return $view;
    }
}