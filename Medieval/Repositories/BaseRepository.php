<?php

namespace Medieval\Repositories;

use Medieval\Framework\Database;

class BaseRepository {

    /** @var $database Database */
    protected $databaseInstance;

    public function __construct( $databaseInstance ) {
        $this->databaseInstance = $databaseInstance;
    }
}