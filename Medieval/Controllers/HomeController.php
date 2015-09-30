<?php

namespace Medieval\Controllers;

class HomeController extends BaseController {

    public function welcome() {

        echo 'Home page';
    }

    public function pageNotFound() {

        echo 'Page not found';
    }
}