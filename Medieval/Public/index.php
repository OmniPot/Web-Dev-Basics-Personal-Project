<link href='https://fonts.googleapis.com/css?family=MedievalSharp' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="/css/styles.css">

<?php

use Medieval\Framework\App;

session_start();

require_once '../Framework/App.php';

$app = App::getInstance();

$app->start();
