<?php

namespace Medieval\Framework;

class BaseRepository {

    /** @var $database Database */
    protected $databaseInstance;

    public function __construct( $databaseInstance ) {
        $this->databaseInstance = $databaseInstance;
    }
}