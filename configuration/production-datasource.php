<?php

if (!defined('DBMS')) {
    define('DBMS', SQLITE);
}
if (!defined('DATA_PATH')) {
    define('DATA_PATH', CONFIGURATION_ROOT . 'task-tracker.sqlite');
}
$dataSource = new \DataStorage\Basis\DataSource(SQLITE . DATA_PATH);

return $dataSource;
