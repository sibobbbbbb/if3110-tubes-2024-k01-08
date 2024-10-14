<?php

require_once __DIR__ . '/../src/utils/Autoloader.php';


use src\core\Application;


// Bootstrap the application
$app = new Application();
$app->run();
