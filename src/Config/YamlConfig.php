<?php

namespace Meduza\Config;

use Meduza\Exception\CriticalException;

/**
 * The meduza configuration in yaml format
 *
 * @author everton
 */
class YamlConfig extends ConfigAbstract {
    
    public function __construct(string $yamlFile) {
        try{
            $this->loadConfig($yamlFile);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    protected function loadConfig($configFile): void {
        try{
            $this->config = yaml_parse_file($configFile);
        } catch (CriticalException $ex) {
            throw $ex;
        }
    }

}
