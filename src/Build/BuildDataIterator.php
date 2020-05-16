<?php

namespace Meduza\Build;

use Meduza\Config\ConfigInterface;

/**
 * Iteerator for build process
 *
 * @author everton
 */
class BuildDataIterator implements \Iterator
{
    protected $config = null;
    
    protected $position = 0;

    protected $pages = [];
    
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
        $this->position = 0;
    }
    
    public function addPageData(PageData $pageData)
    {
        $this->pages[] = $pageData;
    }
    
    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    public function current()
    {
        return $this->pages[$this->position];
    }

    public function key(): \scalar
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->pages[$this->position]);
    }
}
