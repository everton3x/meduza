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

/**
 * Copia o conteúdo estático do tema.
 *
 * O conteúdo estático deve estar numa pasta chamada statci, dentro do diretório
 * do tema.
 *
 * @author evert
 */
class CopyThemeStaticContent implements ProcessInterface
{
    public function __construct()
    {
    }

    public function run(array $buildData): array
    {
        $themeSaticDir = $this->getThemeStaticDir($buildData['config']);
//        echo $themeSaticDir, PHP_EOL;
        $targetDir = $this->getTargetDir($buildData['config']);
//        echo $targetDir, PHP_EOL;
        
        copy_recursive($themeSaticDir, $targetDir);
        
        return $buildData;
    }
    
    /**
     *
     * @param array<mixed> $config
     * @return string
     * @throws Exception
     */
    protected function getTargetDir(array $config): string
    {
        $target = $config['output']['target'];
        $targetDir = realpath($target);
        if ($targetDir == false) {
            if (mkdir($target, 0777, true) === false) {
                throw new Exception("Não foi possível criar $target.");
            }
            $targetDir = realpath($target);
            if ($targetDir === false) {
                throw new Exception("Não foi possível criar $target.");
            }
        }
        $targetDir = end_slash($targetDir);
        
        return $targetDir;
    }
    
    /**
     *
     * @param array<mixed> $config
     * @return string
     * @throws Exception
     */
    protected function getThemeStaticDir(array $config): string
    {
        $themeDir = realpath(end_slash($config['theme']['dir']) . $config['theme']['name']);
        if ($themeDir === false) {
            throw new Exception("Falha ao pegar o caminho do tema em {$config['theme']['dir']}.");
        }
        $themeDir = end_slash($themeDir);
        $themeDir .= 'static' . DIRECTORY_SEPARATOR;
        
        return $themeDir;
    }
}
