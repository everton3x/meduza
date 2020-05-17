<?php
namespace Meduza\Build;

use Ds\Vector;
use Meduza\Config\ConfigInterface;

/**
 * The master builder
 *
 * @author everton
 */
class Builder
{

    protected $config = null;
    protected $pages = null;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function build()
    {
        $this->pages = new BuildDataIterator($this->config);

        foreach ($this->loadContentDir($this->config->getAllConfig()['content_dir']) as $contentFile) {
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

    /**
     * Return a Ds\Vetor with content file path.
     * 
     * @param string $contentPath
     * @return Vector
     */
    protected function loadContentDir(string $contentPath): Vector
    {
        $vector = new Vector();
        $dirContent = new \DirectoryIterator($contentPath);

        $index = 0;
        while ($dirContent->valid() !== false) {
            $currentContent = $dirContent->current();
            if ($currentContent->isFile()) {
                $vector->insert($index, $currentContent->getPathname());
                $index++;
            }
            $dirContent->next();
        }

        return $vector;
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

        foreach ($plugins as $plugName) {
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
