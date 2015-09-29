<?php

namespace Medieval\Application\TestArea\Controllers;

class HomeController extends BaseController {

    public function welcome() {
        echo 'Welcome';
    }

    public function pageNotFound(){
        echo 'Page not found';
    }
}