<?php
namespace Meduza\Build;

use DirectoryIterator;
use Meduza\Config\ConfigInterface;
use Psr\Log\LoggerInterface;

/**
 * The master builder
 *
 * @author everton
 */
class Builder
{

    protected $config = null;
    protected $pages = null;
    protected $logger = null;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
        $this->pages = new BuildDataIterator($config);//need for tests
    }

    public function build(LoggerInterface $logger)
    {
        $this->logger = $logger;
        
        $logger->debug('Build start...');
        
        $this->pages = new BuildDataIterator($this->config);

        $contentDir = $this->loadContentDir($this->config->getAllConfig()['content_dir']);
        
        foreach ($contentDir as $contentFile) {
            $logger->debug("Processs $contentFile");
            $pageData = new PageData(
                $contentFile,
                $this->parseFrontmatter($contentFile),
                $this->readContent($contentFile)
            );
            
            $this->pages->addPageData($pageData);
        }

        $this->runPlugins();
        
//        print_r($this->pages);

        $this->parseContent();

        $this->mergeTemplate();

        $this->saveOutput();

        $this->copyStatic();
        
        $logger->debug("End of build process!");
    }

    /**
     * Return a Ds\Vetor with content file path.
     * 
     * @param string $contentPath
     * @return array
     */
    protected function loadContentDir(string $contentPath): array
    {
        $content = [];
        $dirContent = new DirectoryIterator($contentPath);

        $this->logger->debug("Load content dir $contentPath");
        foreach ($dirContent as $currentContent) {
            if ($currentContent->isFile()) {
                $content[] = $currentContent->getPathname();
                $this->logger->debug("Load content from {$currentContent->getPathname()}");
            }
        }

        return $content;
    }

    protected function parseFrontmatter(string $file): array
    {
        $this->logger->debug("Parse frontmatter for $file");
        return yaml_parse_file($file, 0);
    }

    protected function readContent(string $file): string
    {
        $this->logger->debug("Read content from $file");
        return '';
    }

    protected function runPlugins()
    {
        $this->logger->debug("Run plugins...");
        $plugins = $this->config->getAllConfig()['plugins'];

        foreach ($plugins as $plugName) {
            $plugClass = "\\Meduza\\Plugin\\{$plugName}Plugin";
            $this->logger->debug("Run plugin $plugClass");
            $plug = new $plugClass($this->pages);
            $this->pages = $plug->run();
        }
    }

    protected function parseContent()
    {
        $this->logger->debug("Parsing content");
        //faz um loop nas páginas e pega PageData::$content, trasnforma com a classe config.content_parser e salva em PageData::$htmlContent
    }

    protected function mergeTemplate()
    {
        $this->logger->debug("Merging content and templates");
        //loop nas páginas para pegar o frontmatter e o html content e mesclar no template e salvar no PageData::$output
    }

    protected function saveOutput()
    {
        $this->logger->debug("SAve output");
        //salvaos arquivos finais
    }

    protected function copyStatic()
    {
        $this->logger->debug("Copy static content");
        //copia o conteúdo estático
    }
}
