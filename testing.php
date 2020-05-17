<?php

/*
 * Arquivo para testes rápidos
 */

require_once 'vendor/autoload.php';

$configLoader = new Meduza\Config\ConfigLoader();

$config = $configLoader->loadConfig('meduza.yml');

$builder = new \Meduza\Build\Builder($config);

define('DEBUG_MODE', true);
$builder->build(new \Meduza\Log\ConsoleLogger());