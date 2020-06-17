<?php

namespace Meduza\Config;

/**
 * Interface para loaders de configurações.
 *
 * @author Everton
 */
interface ConfigLoaderInterface
{
    /**
     * O construtor não deve receber nenhum parâmetro, pois trata-se de um factory.
     */
    public function __construct();
    
    /**
     * Carrega as configurações.
     *
     * @param string $config Caminho para o arquivo de configuração principal.
     * @param string $env String do ambiente a ser carregado.
     * @return array<mixed> Retorna as configurações num formato de array.
     */
    public function load(string $config, string $env): array;
}
