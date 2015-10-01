<?php

namespace Medieval\Controllers;

use Medieval\Framework\BaseController;

class HomeController extends BaseController {

    /**
     * @route('home/welcome')
     */
    public function welcome() {

        echo 'Home page';
    }

    /**
     * @route('home/notFound')
     */
    public function pageNotFound() {

        echo 'Page not found';
    }

    /**
     * @route('home/bye/mixed')
     */
    public function goodbye( $message ) {
        $message = str_replace( '-', ' ', $message );
        echo 'Goodbye, ' . $message;
    }
}