<?php

namespace Medieval\Controllers;

use Medieval\Framework\BaseController;
use Medieval\Framework\View;

use Medieval\ViewModels\WelcomeViewModel;

class HomeController extends BaseController {

    /**
     * @route('home/welcome')
     */
    public function welcome() {

        $model = new WelcomeViewModel();
        return new View( $model );
    }
}