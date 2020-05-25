<?php
namespace Meduza\Parser;

use Exception;
use Parsedown;

/**
 * Parser using erusev\Parsedown
 *
 * @author everton
 */
class ErusevParsedown implements ParserInterface
{

    protected $parser = null;

    public function __construct()
    {
        $this->parser = new Parsedown();
    }

    public function parse(string $content): string
    {
        try {
            return $this->parser->parse($content);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
