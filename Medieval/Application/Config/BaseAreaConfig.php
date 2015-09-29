<?php

namespace Medieval\Application\Config;

class BaseAreaConfig {

    protected static $_vendorNamespace = 'Medieval\\';
    protected static $_applicationNamespace = 'Application\\';
    protected static $_frameworkNamespace = 'Framework\\';
    protected static $_controllersNamespace = 'Controllers\\';
    protected static $_modelsNamespace = 'Models\\';
    protected static $_viewModelsNamespace = 'ViewModels\\';
    protected static $_viewsNamespace = 'Views\\';

    protected static $_controllersFolder = 'Controllers\\*.php';
    protected static $_viewFolder = 'Views';

    protected static $_controllersSuffix = 'Controller';
    protected static $_phpExtension = '.php';

}