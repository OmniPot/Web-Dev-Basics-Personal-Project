<?php

namespace Medieval\Framework;

class FileLoader {
    private static $_instance = null;



    public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}