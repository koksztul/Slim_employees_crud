<?php

session_start();
require __DIR__ . '/../vendor/autoload.php';

$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

require __DIR__ . '/../src/routes.php';

$container = $app->getContainer();

require __DIR__ . '/../src/dependencies.php';

$app->run();

?>