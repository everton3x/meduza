<?php

namespace Meduza\Config;

use Exception;

/**
 * Loader de configurações.
 *
 * @author everton
 */
class Loader
{
    public function __construct()
    {
    }
    
    /**
     * Carrega as configurações usando um loader apropriado conforme a extensão do arquivo principal.
     *
     * @param string $configFilePath Caminho para o arquivo de configurações principal
     * @param string $environment String para o ambiente a ser carregado.
     * @return array<mixed> Retorna um array com as configurações.
     * @throws Exception
     */
    public function load(string $configFilePath, string $environment): array
    {
        if (!file_exists($configFilePath)) {
            throw new Exception("Arquivo de configuração [$configFilePath] não encontrado.");
        }
        
        $loader = $this->detectConfigLoader(pathinfo($configFilePath, PATHINFO_EXTENSION));
        
        return $loader->load($configFilePath, $environment);
    }
    
    /**
     * Detecta o loader adequado conforme a extensão do arquivo de configuração.
     *
     * @param string $fileExtension
     * @return ConfigLoaderInterface
     * @throws Exception
     */
    protected function detectConfigLoader(string $fileExtension): ConfigLoaderInterface
    {
        switch ($fileExtension) {
            case 'yml':
                return new YamlConfigLoader();
            default:
                throw new Exception("Configuração [$fileExtension] não suportada.");
        }
    }
}
