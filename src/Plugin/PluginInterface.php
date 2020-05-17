<?php
namespace Meduza\Plugin;

use Meduza\Build\BuildDataIterator;

/**
 * Interface for plugins
 * 
 * @author everton
 */
interface PluginInterface
{
    public function __construct(BuildDataIterator $pages);
    
    public function run();
}
