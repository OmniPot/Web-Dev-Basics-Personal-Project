<?php

namespace Medieval\Application\TestArea\Config;

use Medieval\Application\Config\BaseAreaConfig;

class TestAreaConfig extends BaseAreaConfig {

    public $_vendorNamespace = 'Medieval\\';
    public $_applicationNamespace = 'Application\\';
    public $_frameworkNamespace = 'Framework\\';
    public $_controllersNamespace = 'Controllers\\';
    public $_modelsNamespace = 'Models\\';
    public $_viewModelsNamespace = 'ViewModels\\';
    public $_viewsNamespace = 'Views\\';

    public $_controllersFolder = 'Controllers\\*.php';
    public $_viewFolder = 'Views';

    public $_controllersSuffix = 'Controller';
    public $_phpExtension = '.php';
}