<?php

namespace Meduza\Config;

use Meduza\Exception\CriticalException;

/**
 * Loader for config files
 *
 * @author everton
 */
class ConfigLoader {
    
    const TYPE_YAML = 'yaml';
    
    public function __construct() {
        
    }
    
    public function loadConfig(string $configFile): ConfigInterface {
        switch ($this->detectConfigFileType($configFile)) {
            case self::TYPE_YAML:
                return new YamlConfig($configFile);

            default:
                break;
        }
    }
    
    protected function detectConfigFileType(string $configFile) {
        switch (strtolower($fileExtension = pathinfo($configFile, PATHINFO_EXTENSION))) {
            case 'yml':
                return self::TYPE_YAML;

            default:
                throw new CriticalException("Configuration format [$fileExtension] not supported.");
        }
    }
}
