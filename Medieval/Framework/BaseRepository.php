<?php

namespace Medieval\Framework;

use Medieval\Framework\Database\Database;

class BaseRepository {

    /** @var $database Database */
    protected $databaseInstance;

    public function __construct( $databaseInstance ) {

        $this->databaseInstance = $databaseInstance;
    }
}