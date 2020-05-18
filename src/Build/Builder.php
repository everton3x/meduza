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
    protected $pageIterator = null;
    protected $logger = null;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
        $this->pageIterator = new BuildDataIterator($config); //need for tests
    }

    public function build(LoggerInterface $logger)
    {
        $this->logger = $logger;

        $logger->debug('Build start...');

        $this->pageIterator = new BuildDataIterator($this->config);

        $contentDir = $this->loadContentDir($this->config->getAllConfig()['content_dir']);

        foreach ($contentDir as $contentFile) {
            $logger->debug("Processs $contentFile");
            $pageData = new PageData(
                $contentFile,
                $this->parseFrontmatter($contentFile),
                $this->readContent($contentFile),
                $this->config
            );

            $this->pageIterator->addPageData($pageData);
        }

        $this->runPlugins();

        $this->parseContent();

        $this->mergeTemplate();

        print_r($this->pageIterator);

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

        $content = '';

        $fhandle = fopen($file, 'r');
        $controler = 0;
        while (($line = fgets($fhandle)) !== false) {
            if ($controler >= 2) {
                $content .= $line;
            }

            if (trim($line) === '---') {
                $controler++;
            }
        }

        fclose($fhandle);

        return $content;
    }

    protected function runPlugins()
    {
        $this->logger->debug("Run plugins...");
        $plugins = $this->config->getAllConfig()['plugins'];

        foreach ($plugins as $plugName) {
            $plugClass = "\\Meduza\\Plugin\\{$plugName}Plugin";
            $this->logger->debug("Run plugin $plugClass");
            $plug = new $plugClass($this->pageIterator);
            $this->pageIterator = $plug->run();
        }
    }

    protected function parseContent()
    {
        $this->logger->debug("Parsing content");
        //faz um loop nas páginas e pega PageData::$content, trasnforma com a classe config.content_parser e salva em PageData::$htmlContent

        $parsersConfigList = $this->config->getAllConfig()['content_parsers'];
//        print_r($parsersConfigList);

        $this->pageIterator->rewind();
        while ($this->pageIterator->valid()) {
            $page = $this->pageIterator->current();

            $extension = $page->getFileExtension();

            if (!key_exists($extension, $parsersConfigList)) {
                throw new ErrorException("Content parser for file extension [$extension] not supported.");
            }
            $parserClassName = "\\Meduza\Parser\\{$parsersConfigList[$extension]}";
            $parser = new $parserClassName();
            $page->setHtmlContent($parser->parse($page->getContent()));

            $this->pageIterator->next();
        }
    }

    protected function mergeTemplate()
    {
        $this->logger->debug("Merging content and templates");
        //loop nas páginas para pegar o frontmatter e o html content e mesclar no template e salvar no PageData::$output

        $theme_path = "{$this->config->getAllConfig()['themes_dir']}/{$this->config->getAllConfig()['theme']}";
        $twigLoader = new \Twig\Loader\FilesystemLoader($theme_path);
        $twig = new \Twig\Environment($twigLoader);


        $this->pageIterator->rewind();
        while ($this->pageIterator->valid()) {
            $page = $this->pageIterator->current();
            
            $frontMatter = $page->getFrontmatter();
            $htmlContent = $page->getHtmlContent();
            
            if(key_exists('template', $frontMatter)){
                $template = $frontMatter['template'];
            }else{
                $template = $this->config->getAllConfig()['default_template'];
            }
            
            $output = $twig->render("$template.twig", [
                'config' => $this->config->getAllConfig(),
                'page' => [
                    'content' => $htmlContent
                ]
            ]);

            $page->setOutput($output);
            $this->pageIterator->next();
        }
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
