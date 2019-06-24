<?php

try {
    (require('production-configuration.php'))->run();
} catch (Exception $e) {
    echo $e->getMessage();
}
