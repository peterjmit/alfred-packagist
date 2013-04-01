<?php

if (is_dir($vendor = getcwd() . '/vendor')) {
    require $vendor . '/autoload.php';
} else {
    die('Please install vendor via composer.phar install');
}

$packagist = new \Peterjmit\AlfredPackagist\Runner();
