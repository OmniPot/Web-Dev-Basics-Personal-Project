<?php

namespace Medieval\Application\Config;

class MainConfig {

    const VENDOR_NAMESPACE = 'Medieval\\';
    const APPLICATION_NAMESPACE = 'Application\\';
    const FRAMEWORK_NAMESPACE = 'Framework\\';
    const CONTROLLERS_NAMESPACE = 'Controllers\\';
    const MODELS_NAMESPACE = 'Models\\';
    const VIEW_MODELS_NAMESPACE = 'ViewModels\\';
    const VIEWS_NAMESPACE = 'Views\\';

    const CONTROLLERS_FOLDER = 'Application\Controllers\*.php';
    const VIEW_FOLDER = 'Application\Views';

    const CONTROLLERS_SUFFIX = 'Controller';
    const PHP_EXTENSION = '.php';
}