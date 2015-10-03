<?php

namespace Medieval\Framework\Helpers;

use Medieval\Framework\BaseController;
use Medieval\Framework\Routers\RequestUriResult;

class BindingsResolver {

    /**
     * @param BaseController $controller
     * @param RequestUriResult $uriParseResult
     * @return RequestUriResult
     * @throws \Exception
     */
    public static function resolveModelBinding( BaseController $controller, RequestUriResult $uriParseResult ) {
        if ( in_array( $_SERVER[ 'REQUEST_METHOD' ], [ 'POST', 'PUT' ] ) ) {

            $controllerReflection = new \ReflectionClass( $controller );
            $controllerMethods = $controllerReflection->getMethods();
            $bindingModel = null;

            foreach ( $controllerMethods as $method ) {

                if ( $method->name == $uriParseResult->getActionName() ) {
                    $parameters = $method->getParameters();

                    foreach ( $parameters as $param ) {
                        $paramClass = $param->getClass();

                        if ( $paramClass ) {
                            $paramClassName = $paramClass->getName();
                            $bindingModel = new $paramClassName();
                            $reflectionBindingModel = new \ReflectionClass( $paramClassName );

                            foreach ( $reflectionBindingModel->getProperties() as $property ) {
                                $propertySetter = 'set' . ucfirst( $property->name );

                                if ( !isset( $_POST[ $property->name ] ) || !$_POST[ $property->name ] ) {
                                    throw new \Exception( 'Invalid post data supplied' );
                                } else {
                                    $bindingModel->$propertySetter( $_POST[ $property->name ] );
                                }
                            }

                            $uriParseResult->setRequestParams( [ $bindingModel ] );

                            return $uriParseResult;
                        }
                    }
                }
            }
        }

        return $uriParseResult;
    }
}