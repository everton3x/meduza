<?php

/*
 * Arquivo para testes rápidos
 */

require_once 'vendor/autoload.php';

$configLoader = new Meduza\Config\ConfigLoader();

print_r($configLoader->loadConfig('meduza.yml'));