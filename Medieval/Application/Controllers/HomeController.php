<?php

namespace Medieval\Application\Controllers;

class HomeController extends BaseController {

    public function welcome() {
        echo 'Welcome';
    }

    public function pageNotFound(){
        echo 'Page not found';
    }
}