<?php

namespace Medieval\Config;

class AppConfig {

    const PHP_EXTENSION = '.php';

    const DEFAULT_AREA = 'Main';
    const DEFAULT_CONTROLLER = 'Home';
    const DEFAULT_ACTION = 'welcome';

    const VENDOR_NAMESPACE = 'Medieval' . DIRECTORY_SEPARATOR;
    const CONTROLLERS_NAMESPACE = 'Controllers' . DIRECTORY_SEPARATOR;
    const AREAS_NAMESPACE = 'Areas' . DIRECTORY_SEPARATOR;
    const REPOSITORIES_NAMESPACE = 'Repositories' . DIRECTORY_SEPARATOR;
    const VIEW_MODELS_NAMESPACE = 'ViewModels' . DIRECTORY_SEPARATOR;
    const VIEWS_NAMESPACE = 'Views' . DIRECTORY_SEPARATOR;

    const AREA_SUFFIX = 'Area' . DIRECTORY_SEPARATOR;
    const CONTROLLER_SUFFIX = 'Controller';

}