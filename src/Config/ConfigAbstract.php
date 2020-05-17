<?php
namespace Meduza\Config;

/**
 * Abstract class for config object
 *
 * @author everton
 */
abstract class ConfigAbstract implements ConfigInterface
{

    protected $config = [];

    abstract protected function loadConfig($configFile): void;

    protected function loadExtraConfig(): void
    {
        $config = $this->getAllConfig();

        if (key_exists('extra-config', $config)) {
            $loader = new ConfigLoader();
            
            foreach ($config['extra-config'] as $extraConfigFile) {
                $extraConfig = $loader->loadConfig($extraConfigFile);
                $this->config = array_merge($this->config, $extraConfig->getAllConfig());
            }
        }
    }

    public function getAllConfig(): array
    {
        return $this->config;
    }
}
