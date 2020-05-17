<?php
namespace Meduza\Plugin;

use Meduza\Build\BuildDataIterator;

/**
 * Create page with tags
 *
 * @author everton
 */
class TagsPlugin implements PluginInterface
{
    protected $pageIterator = null;
    
    public function __construct(BuildDataIterator $pages)
    {
        $this->pageIterator = $pages;
    }

    public function run()
    {
        $this->pageIterator->rewind();
        
        while($this->pageIterator->valid()){
            $page = $this->pageIterator->current();
            $frontMatter = $page->getFrontmatter();
            if(!key_exists('tags', $frontMatter)) {
                $frontMatter['tags'] = ['No tags'];
            }
            $this->pageIterator->next();
        }
        return $this->pageIterator;
    }
}
