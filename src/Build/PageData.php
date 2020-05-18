<?php
namespace Meduza\Build;

/**
 * The page data for individual content file
 *
 * @author everton
 */
class PageData
{
    protected $file = '';
    
    protected $frontmatter = [];
    
    protected $content = '';
    
    protected $htmlContent = '';
    
    protected $output = '';

    public function __construct(string $file, array $frontmatter, string $content)
    {
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
        $this->output = $output;
    }
    
    public function getOutput(): string
    {
        return $this->output;
    }
    
    public function getSlug(): string
    {
        if(key_exists('slug', $this->frontmatter)){
            $slug = $this->frontmatter['slug'];
        }else{
            $fileExtension = pathinfo($this->file, PATHINFO_EXTENSION);
            
            $slug = basename($this->file, $fileExtension);
        }
        
        return $slug;
    }
}
