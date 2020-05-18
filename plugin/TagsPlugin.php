<?php
namespace Meduza\Plugin;

use Meduza\Build\BuildDataIterator;
use Meduza\Build\PageData;

/**
 * Create page with tags
 *
 * @author everton
 */
class TagsPlugin implements PluginInterface
{

    protected $pageIterator = null;
    protected $tagIndex = [];

    public function __construct(BuildDataIterator $pageIterator)
    {
        $this->pageIterator = $pageIterator;
    }

    public function run(): BuildDataIterator
    {
        $pageIterator = $this->pageIterator;

        $pageIterator->rewind();

        while ($pageIterator->valid()) {
            $page = $pageIterator->current();
            $frontMatter = $page->getFrontmatter(); //return by reference ATENTION
//            print_r($frontMatter);
            if (!key_exists('tags', $frontMatter)) {
                $frontMatter['tags'] = ['No tags'];
            }

            $this->indexTags($frontMatter['tags'], $page->getSlug(), $frontMatter['title']);

            $page->setFrontmatter($frontMatter);

            $pageIterator->next();
        }
        
        $pageIterator = $this->injectTagList($pageIterator);

//        print_r($this->tagIndex);
        return $pageIterator;
    }
    
    protected function injectTagList(BuildDataIterator $pageIterator)
    {
        $file = $pageIterator->getConfig()->getAllConfig()['content_dir'].'/tags.md';
        
        $frontMatter = [
            'title' => 'Tags',
            'template' => 'tags',
            'slug' => '/tags/'
        ];
        
        $content = '';
        
        $page = new PageData($file, $frontMatter, $content, $pageIterator->getConfig());
        
        $pageIterator->addPageData($page);
        
        return $pageIterator;
    }

    protected function indexTags(array $tags, string $slug, string $title)
    {
        $index = 0;
        foreach ($tags as $tag) {
            $this->tagIndex[$tag][$index]['slug'] = $slug;
            $this->tagIndex[$tag][$index]['title'] = $title;
            $index++;
        }
    }
}
