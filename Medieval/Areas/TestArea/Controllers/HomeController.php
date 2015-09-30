<?php

namespace Medieval\Areas\TestArea\Controllers;

use Medieval\Controllers\BaseController;

class HomeController extends BaseController {

    public function welcome() {
        if ( !$this->isLogged() ) {
            $this->redirect( $this->unauthorizedLocation );
        }

        echo 'Welcome';
    }

    public function pageNotFound() {

        echo 'Page not found';
    }
}