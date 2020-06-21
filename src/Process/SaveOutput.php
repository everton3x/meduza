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
 * Salva as meta pages em arquivos HTML.
 *
 * @author evert
 */
class SaveOutput implements ProcessInterface
{
    
    public function __construct()
    {
    }

    /**
     *
     * @param array<mixed> $buildData
     * @return array<mixed>
     * @throws Exception
     */
    public function run(array $buildData): array
    {
        $metaPages = $buildData['metaPages'];
        $config = $buildData['config'];
        
        $targetDir = realpath($config['output']['target']);
        if ($targetDir === false) {
            throw new Exception("Diretório de destino inválido em {$config['output']['target']}");
        }
        $targetDir = end_slash($targetDir);
        
        foreach ($metaPages as $page) {
//            print_r($page);
            $html = $page['outputHTML'];
            $filename = $this->getOutputFileName($page['frontmatter']['slug'], $config['site']['url'], $targetDir);
//            echo $filename, PHP_EOL;
            $this->makeDir($filename);
            if (file_put_contents($filename, $html) === false) {
                throw new Exception("Falha ao salvar $filename");
            }
        }
        
        
        return $buildData;
    }
    
    /**
     * Cria a estrutura de diretório para o arquivo de destino.
     *
     * @param string $filename
     * @return void
     * @throws Exception
     */
    protected function makeDir(string $filename): void
    {
        $dirname = dirname($filename);
        
        if (file_exists($dirname)) {
            return;
        }
        
        if (mkdir($dirname, 0777, true) === false) {
            throw new Exception("Falha ao criar $dirname");
        }
    }
    
    /**
     * Obtém o caminho completo para o arquivo de destino a partir do slug.
     *
     * @param string $slug
     * @param string $siteURL
     * @param string $targetDir
     * @return string
     * @throws Exception
     */
    protected function getOutputFileName(string $slug, string $siteURL, string $targetDir): string
    {
        $siteURL = end_slash($siteURL);
        
        $filename = str_replace($siteURL, $targetDir, $slug);
        
        if ($filename == false) {
            throw new Exception("Falha ao criar o nem de arquivo para $slug");
        }
        
        return $filename;
    }
}
