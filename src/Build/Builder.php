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

        $contentDir = $this->loadContentDir($this->config->getAllConfig()['content']['contentDir']);

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

        $this->saveOutput();

        $this->copyStatic();

        $this->copyThemeStatic();

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

        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($contentPath));
        $it->rewind();
        while ($it->valid()){
            if($it->isFile()){
                $content[] = $it->getPathname();
                $this->logger->debug("Load content from {$it->getPathname()}");
            }
            
            
            $it->next();
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

        $parsersConfigList = $this->config->getAllConfig()['output']['parsers'];
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

        $theme_path = "{$this->config->getAllConfig()['content']['themesDir']}/{$this->config->getAllConfig()['theme']}";
        $twigLoader = new \Twig\Loader\FilesystemLoader($theme_path);
        $twig = new \Twig\Environment($twigLoader, [
            'autoescape' => false //needs to no auto escape content
        ]);


        $this->pageIterator->rewind();
        while ($this->pageIterator->valid()) {
            $page = $this->pageIterator->current();

            $frontMatter = $page->getFrontmatter();
            $htmlContent = $page->getHtmlContent();

            if (key_exists('template', $frontMatter)) {
                $template = $frontMatter['template'];
            } else {
                $template = $this->config->getAllConfig()['content']['defaultTemplate'];
            }

//            echo $htmlContent, PHP_EOL;
            $output = $twig->render("$template.twig", [
                'config' => $this->config->getAllConfig(),
                'page' => [
                    'content' => $htmlContent,
                    'config' => $frontMatter
                ]
            ]);
//            echo $output, PHP_EOL;
            $page->setOutput($output);
            $this->pageIterator->next();
        }
    }

    protected function saveOutput()
    {
        $this->logger->debug("Save output");
        //salva os arquivos finais
//        print_r($this->pageIterator);
        $config = $this->config->getAllConfig();

        if (!key_exists('target', $config['output'])) {
            throw new CriticalException("Output dir not configured!");
        }

        $output_dir = $config['output']['target'];
//        echo $output_dir, PHP_EOL;

        if (is_dir(realpath($output_dir))) {
            $this->delTree(realpath($output_dir));
        }

        $this->pageIterator->rewind();
        while ($this->pageIterator->valid()) {
            $page = $this->pageIterator->current();

            $frontMatter = $page->getFrontmatter();
            $html = $page->getOutput();
            $slug = $page->getSlug();
//            echo $page->getFilePath(), ' -> ',$slug, PHP_EOL;
//            print_r($frontMatter);


            $ext = pathinfo($slug, PATHINFO_EXTENSION);
            $filename = basename($slug, ".$ext").'.html';
//            print_r($config);
            $file_sub_path = dirname(str_replace($config['site']['urlBase'], $output_dir, $slug));
//            echo $file_sub_path, PHP_EOL;

            if (!is_dir($file_sub_path)) {
                mkdir($file_sub_path, 0777, true);
            }

            $file_output = $file_sub_path . DIRECTORY_SEPARATOR . $filename;
//            echo $slug, ' -> ',$file_output, PHP_EOL;
//            echo $html, PHP_EOL;
            file_put_contents($file_output, $html);

            $this->pageIterator->next();
        }
    }

    protected function copyStatic()
    {
        $this->logger->debug("Copy static content");
        //copia o conteúdo estático

        $this->recursiveCopy(realpath($this->config->getAllConfig()['content']['staticDir']), realpath($this->config->getAllConfig()['output']['target']));
    }

    protected function copyThemeStatic()
    {
        $this->logger->debug("Copy theme static content");
        //copia o conteúdo estático do tema
        $config = $this->config->getAllConfig();
        $theme_static_dir = realpath("{$config['content']['themesDir']}/{$config['theme']}/static/");
        $output_dir = realpath($this->config->getAllConfig()['output']['target']);

        $this->recursiveCopy("$theme_static_dir", $output_dir);
    }

    protected function delTree(string $dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    protected function recursiveCopy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst, 0777, true);
        while (false !== ( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . '/' . $file)) {
                    $this->recursiveCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}
