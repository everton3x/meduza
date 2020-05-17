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

    public function run(): BuildDataIterator
    {
        $this->pageIterator->rewind();
        
        while($this->pageIterator->valid()){
            $page = $this->pageIterator->current();
            $frontMatter = &$page->getFrontmatter();//return by reference ATENTION
//            print_r($frontMatter);
            if(!key_exists('tags', $frontMatter)) {
                $frontMatter['tags'] = ['No tags'];
            }
            $this->pageIterator->next();
        }
        
        //@TODO Implementar o restante do plugin
        
        return $this->pageIterator;
    }
}
