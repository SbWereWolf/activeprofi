<?php

use Environment\Setup\Setup;
use Slim\Container;

if (!defined('APPLICATION_ROOT')) {
    define('APPLICATION_ROOT', realpath(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
}
require_once(APPLICATION_ROOT . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

if (!defined('CONFIGURATION_ROOT')) {
    define('CONFIGURATION_ROOT', APPLICATION_ROOT . 'configuration' . DIRECTORY_SEPARATOR);
}
if (!defined('DATA_SOURCE')) {
    define('DATA_SOURCE', CONFIGURATION_ROOT . 'production-datasource.php');
}
if (!defined('DATA_SOURCE_KEY')) {
    define('DATA_SOURCE_KEY', 'dataSource');
}
if (!defined('SETTINGS_KEY')) {
    define('SETTINGS_KEY', 'settings');
}
If (!defined('SQLITE')) {
    define('SQLITE', 'sqlite:');
}
$dataSource = require(DATA_SOURCE);

$configuration['displayErrorDetails'] = true;
$configuration['addContentLengthHeader'] = false;
$configuration[DATA_SOURCE_KEY] = $dataSource;
unset($dataSource);
$container = new Container([SETTINGS_KEY => $configuration]);

$app = null;
try {
    $app = (new Setup(new \Slim\App($container)))
        ->perform();
} catch (Exception $e) {
    echo $e->getMessage();
}
return $app;
