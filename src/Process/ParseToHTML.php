<?php

/*
 * The MIT License
 *
 * Copyright 2020 evert.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Meduza\Process;

/**
 * Transforma o conteúdo em html
 *
 * @author evert
 */
class ParseToHTML implements ProcessInterface
{

    public function __construct()
    {
    }

    public function run(array $buildData): array
    {
        $html = '';
        $parsers = $buildData['config']['output']['parsers'];
//        print_r($parsers);
        $metaPages = &$buildData['metaPages'];
//        print_r($metaPages);
        foreach ($metaPages as $key => $page) {
            $extension = $page['fileSourceExtension'];
            $parsable = $page['parsableContent'];
            if (key_exists($extension, $parsers)) {
                $parserClass = "\\Meduza\\Parser\\{$parsers[$extension]}";
                $parser = new $parserClass();
                $html = $parser->parse($parsable);
            }
            $metaPages[$key]['htmlContent'] = $html;
//            print_r($html);
//            print_r($this->buildRepo->get("meta-pages.{$slug}.html"));
        }


        return $buildData;
    }
}
