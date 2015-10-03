<link rel="stylesheet" type="text/css" href="/Contents/styles.css">

<?php

use Medieval\Framework\App;

session_start();

require_once 'Framework\App.php';

$app = App::getInstance();

$app->start();