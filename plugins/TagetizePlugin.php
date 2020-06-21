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

namespace Meduza\Plugin;

use Exception;

/**
 * Indexa o conteúdo pelas tags
 *
 * Tags são classificações não hierarquizadas.
 *
 * O conteúdo que não tiver tag não será indexado.
 *
 * @author evert
 */
class TagetizePlugin implements PluginInterface
{

    public function __construct()
    {
    }

    /**
     *
     * @param array<mixed> $buildData
     * @return array<mixed>
     */
    public function run(array $buildData): array
    {
        
        $metaPages = $buildData['metaPages'];
        $index = [];
        
        foreach ($metaPages as $key => $page) {
            if (key_exists('tags', $page['frontmatter']) === false) {
                continue;
            }
            
            $tags = $page['frontmatter']['tags'];
            
            foreach ($tags as $tag) {
                $index[$tag][$key]['title'] = $page['frontmatter']['title'];
                $index[$tag][$key]['slug'] = $page['frontmatter']['slug'];
            }
        }
        
        ksort($index, SORT_NATURAL);
        
//        print_r($index);
        $buildData = $this->buildMetaPage($buildData, $index);
        
//        print_r($buildData);
        
        return $buildData;
    }
    
    /**
     *
     * @param array<mixed> $buildData
     * @param array<mixed> $index
     * @return array<mixed>
     */
    protected function buildMetaPage(array $buildData, array $index): array
    {
        $indexMetaPageKey = $this->searchTagMetaPage($buildData);
        $buildData['metaPages'][$indexMetaPageKey]['frontmatter']['published'] = date('Y-m-d H:i:s');
        $buildData['metaPages'][$indexMetaPageKey]['frontmatter']['tagList'] = $index;
        
        return $buildData;
    }

    /**
     *
     * @param array<mixed> $buildData
     * @return int
     */
    protected function searchTagMetaPage(array $buildData): int
    {
        foreach ($buildData['metaPages'] as $key => $page) {
            if (key_exists('tagetize', $page['frontmatter'])) {
                return $key;
            }
        }
        
        throw new Exception("Não foi encontrada nenhuma meta página para tags.");
    }
}
