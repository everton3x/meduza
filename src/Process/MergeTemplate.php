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

use Exception;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

use function end_slash;

/**
 * Mescla o conteúdo construído com o template
 *
 * @author evert
 */
class MergeTemplate implements ProcessInterface
{
    
    public function __construct()
    {
    }

    public function run($buildData): array
    {
        $metaPages = &$buildData['metaPages'];
        $config = $buildData['config'];
        
        $themeDir = realpath(end_slash($config['theme']['dir']) . $config['theme']['name']);
        if ($themeDir === false) {
            throw new Exception("Falha ao pegar o caminho do tema em {$config['theme']['dir']}.");
        }
        $themeDir = end_slash($themeDir);
        
        $environment = $this->prepareEnvironment($themeDir);
        
        foreach ($metaPages as $key => $page) {
            $template = "{$config['defaults']['template']}.twig";
            
            if (key_exists('template', $page['frontmatter'])) {
                $template = "{$page['frontmatter']['template']}.twig";
            }
            
            $metaPages[$key]['outputHTML'] = $environment->render($template, [
                'config' => $config,
                'meta' => $page['frontmatter'],
                'content' => $page['htmlContent']
            ]);
        }
        
        return $buildData;
    }
    
    protected function prepareEnvironment(string $themeDir): Environment
    {
        $loader = new FilesystemLoader($themeDir);
        return new Environment($loader, [
            'autoescape' => false //necessário porque se não o twig escapa os caracteres html do conteúdo
        ]);
    }
}
