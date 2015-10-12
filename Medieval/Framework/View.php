<?php

namespace Medieval\Framework;

use Medieval\Framework\Config\FrameworkConfig;

class View {

    public $_data = [ ];

    private static $_instance;
    private $_viewsDir;

    private $_layoutPartials = [ ];
    private $_renderedPartials = [ ];

    private function __construct() {
    }

    public function setViewsDir( $_viewsDir ) {
        $this->_viewsDir = $_viewsDir;
    }

    public function appendToLayout( $layoutName, $templateName, $model = null ) {
        if ( !$layoutName ) {
            throw new \Exception( 'Invalid or no layout name provided' );
        }

        if ( !$templateName ) {
            throw new \Exception( 'Invalid or no template name provided' );
        }

        if ( !isset( $this->_layoutPartials[ $layoutName ] ) ) {
            $this->_layoutPartials[ $layoutName ] = [ ];
        }

        $this->_layoutPartials[ $layoutName ][ $templateName ] = $model ? $model : null;
    }

    public function render( $layoutName, $returnAsString = false ) {
        if ( !$layoutName ) {
            throw new \Exception( 'Invalid or no layout name provided' );
        }

        foreach ( $this->_layoutPartials as $layoutName => $partials ) {
            foreach ( $partials as $partialName => $model ) {

                $this->validateModelType( $model, $partialName );
                $renderedPartial = $this->includeFile( $partialName );

                if ( $renderedPartial ) {
                    $this->_renderedPartials[ $layoutName ][ $partialName ] = $renderedPartial;
                }
            }
        }

        if ( $returnAsString ) {
            return $this->includeFile( $layoutName );
        } else {
            echo $this->includeFile( $layoutName );
        }
    }

    private function includeFile( $fileName ) {
        $path = $this->viewPathToUpper( $fileName );

        if ( file_exists( $path ) && is_readable( $path ) ) {
            ob_start();
            include $path;
            return ob_get_clean();
        } else {
            throw new \Exception( 'View ' . $fileName . ' cannot be included', 500 );
        }
    }

    private function renderPartials( $layoutName ) {

        if ( !isset( $this->_renderedPartials[ $layoutName ] ) ) {
            throw new \Exception( 'No layout with name ' . $layoutName );
        }

        $model = 'a';

        return implode( "\n", $this->_renderedPartials[ $layoutName ] );
    }

    private function validateModelType( $model, $viewName = null ) {

        $viewFile = $this->viewPathToUpper( $viewName );
        $viewContent = file_get_contents( $viewFile );

        $typeRegex = ' /@var\s*.*\s+(' . FrameworkConfig::VENDOR_NAMESPACE . '\\.*?)\s+\s*.*/';
        preg_match( $typeRegex, $viewContent, $matches );

        $typeGiven = $model ? get_class( $model ) : null;
        $typeExpected = !empty( $matches ) ? $matches[ 1 ] : null;

        if ( $typeGiven != $typeExpected ) {
            throw new \Exception( 'Invalid view model type supplied' );
        }

        return $viewFile;
    }

    private function viewPathToUpper( $fileName ) {

        $path = explode( '.', $fileName );
        $templateName = array_pop( $path );

        foreach ( $path as $partKey => $partValue ) {
            $path[ $partKey ] = ucfirst( $partValue );
        }

        $path[] = $templateName;
        $path = implode( DIRECTORY_SEPARATOR, $path );
        $path = $this->_viewsDir . $path . FrameworkConfig::PHP_EXTENSION;

        return $path;
    }

    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}