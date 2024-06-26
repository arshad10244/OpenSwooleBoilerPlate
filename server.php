<?php
require_once 'vendor/autoload.php';

use Shahzaib\Framework\Core\App;

$app = new App();
$app->configureApp();
$app->startServer();
