<?php

namespace Medieval\Framework\Helpers;

use Medieval\Framework\BaseController;
use Medieval\Framework\Routers\RequestUriResult;

class BindingsResolver {

    /**
     * @param BaseController $controller
     * @return RequestUriResult
     * @throws \Exception
     */
    public static function resolveModelBinding( BaseController $controller, $actionName ) {
        if ( in_array( $_SERVER[ 'REQUEST_METHOD' ], [ 'POST', 'PUT' ] ) ) {

            $controllerReflection = new \ReflectionClass( $controller );
            $controllerMethods = $controllerReflection->getMethods();
            $bindingModel = null;

            foreach ( $controllerMethods as $method ) {
                if ( $method->name == $actionName ) {
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
                                    throw new \Exception( 'Invalid or no post data supplied' );
                                } else {
                                    $bindingModel->$propertySetter( $_POST[ $property->name ] );
                                }
                            }

                            return $bindingModel;
                        }
                    }
                }
            }
        }
    }
}