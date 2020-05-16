<?php
namespace Meduza\Build;

/**
 * The master builder
 *
 * @author everton
 */
class Builder
{

    protected $config = null;
    protected $pages = null;

    public function __construct(\Meduza\Config\ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function build()
    {
        $this->pages = new BuildDataIterator($this->config);

        foreach ($this->loadContentDir('config.content_dir') as $contentFile) {
            $pageData = new PageData(
                $contentFile,
                $this->parseFrontmatter($contentFile),
                $this->readContent($contentFile)
            );
        }
        
        $this->runPlugins();
        
        $this->parseContent();
        
        $this->mergeTemplate();
        
        $this->saveOutput();
        
        $this->copyStatic();
        
        
    }

    protected function loadContentDir(string $contentPath): array
    {
        return []; //retorna um array com o caminho de todos os arquivos
    }

    protected function parseFrontmatter(string $file): array
    {
        return [];
    }

    protected function readContent(string $file): string
    {
        return '';
    }
    
    protected function runPlugins()
    {
        $plugins = 'config.plugins';
        
        foreach ($plugins as $plugName){
            $plugClass = "\Meduza\Plugin\{$plugName}Plugin";
            $plug = new $plugClass($this->pages);
            $this->pages = &$plug->run();
        }
    }
    
    protected function parseContent()
    {
        //faz um loop nas páginas e pega PageData::$content, trasnforma com a classe config.content_parser e salva em PageData::$htmlContent
    }
    
    protected function mergeTemplate()
    {
        //loop nas páginas para pegar o frontmatter e o html content e mesclar no template e salvar no PageData::$output
    }
    
    protected function saveOutput()
    {
        //salvaos arquivos finais
    }
    
    protected function copyStatic()
    {
        //copia o conteúdo estático
    }
}
