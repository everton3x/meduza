<?php

namespace Meduza\Parser;

/**
 * Interface to content parsers.
 * 
 * @author everton
 */
interface ParserInterface
{
    public function __construct();
    
    public function parse(string $content): string;
}
