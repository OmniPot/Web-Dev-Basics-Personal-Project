<?php

namespace Medieval\Controllers;

use Medieval\Framework\BaseController;

class HomeController extends BaseController {

    public function welcome() {

        echo 'Home page';
    }

    public function pageNotFound() {

        echo 'Page not found';
    }
}