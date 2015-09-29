<?php

namespace Medieval\Application\TestArea\Models;

use Medieval\Framework\Database;

class BaseModel {

    /** @var $database Database */
    protected $databaseInstance;

    public function __construct( $databaseInstance ) {
        $this->databaseInstance = $databaseInstance;
    }
}