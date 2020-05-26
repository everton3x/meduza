<?php
namespace Meduza\Build;

use Meduza\Config\ConfigInterface;

/**
 * The page data for individual content file
 *
 * @author everton
 */
class PageData
{
    protected $config = null;
    
    protected $file = '';
    
    protected $frontmatter = [];
    
    protected $content = '';
    
    protected $htmlContent = '';
    
    protected $output = '';

    public function __construct(string $file, array $frontmatter, string $content, ConfigInterface $config)
    {
        $this->config = $config;
        $this->file = $file;
        $this->frontmatter = $frontmatter;
        $this->content = $content;
    }
    
    public function getFrontmatter(): array
    {
        return $this->frontmatter;
    }
    
    public function setFrontmatter(array $frontMatter)
    {
        $this->frontmatter = $frontMatter;
    }
    
    public function getContent(): string
    {
        return $this->content;
    }
    
    public function setContent(string $content)
    {
        $this->content = $content;
    }
    
    public function setHtmlContent(string $html)
    {
        $this->htmlContent = $html;
    }
    
    public function getHtmlContent(): string
    {
        return $this->htmlContent;
    }
    
    public function setOutput(string $html)
    {
        $this->output = $html;
    }
    
    public function getOutput(): string
    {
        return $this->output;
    }
    
    public function getSlug(): string
    {
        if(key_exists('urlBase', $this->config->getAllConfig()['site'])) {
            $url_base = $this->config->getAllConfig()['site']['urlBase'];
        } else {
            $url_base = '';
        }
        
        if(key_exists('slug', $this->frontmatter)){
            $slug = $this->frontmatter['slug'];
        }else{
            $pathSplitted = explode('.', $this->file);
            array_pop($pathSplitted);//pop last element (extension);
            
            
            $slug = str_replace($this->config->getAllConfig()['content']['contentDir'], '', $pathSplitted)[0];
        }
        
        return "$url_base$slug";
    }
    
    public function getFileExtension(): string
    {
        return pathinfo($this->file, PATHINFO_EXTENSION);
    }
    
    public function getFilePath(): string
    {
        return $this->file;
    }
}
