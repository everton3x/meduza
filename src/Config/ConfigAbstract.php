<?php

namespace Meduza\Config;

/**
 * Abstract class for config object
 *
 * @author everton
 */
abstract class ConfigAbstract implements ConfigInterface {
    
    
    protected $config = [];
    
    abstract protected function loadConfig($configFile): void;
    
    public function getAllConfig(): array {
        return $this->config;
    }
}
