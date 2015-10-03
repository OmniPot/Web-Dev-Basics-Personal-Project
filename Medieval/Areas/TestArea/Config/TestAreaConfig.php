<?php

namespace Medieval\Areas\TestArea\Config;

use Medieval\Framework\Config\FrameworkConfig;

class TestAreaConfig extends FrameworkConfig {

    const AREA_NAME = '';

    const CONTROLLERS_FOLDER = 'Controllers\\*' . self::PHP_EXTENSION;
    const REPOSITORIES_FOLDER = 'Repositories\\*' . self::PHP_EXTENSION;
    const VIEW_FOLDER = 'Views\\*' . self::PHP_EXTENSION;
    const VIEW_MODELS_FOLDER = 'ViewModels\\*' . self::PHP_EXTENSION;
    const CONFIG_FOLDER = 'Config\\*' . self::PHP_EXTENSION;

    const AREA_SUFFIX = 'Area\\';
    const CONTROLLER_SUFFIX = 'Controller\\';

}