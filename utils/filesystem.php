<?php

/*
 * The MIT License
 *
 * Copyright 2020 everton.
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

/**
 * Utilidades relacionadas a arquivos, diretórios e caminhos.
 *
 * @author everton
 */

/**
 * Lê os arquivos de um diretório e seus subdiretórios.
 *
 * @param string $dir
 * @return array<string>
 * @throws Exception
 */
function readFilesIntoDIr(string $dir): array
{
    $list = [];
    try {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

        for ($iterator->rewind(); $iterator->valid(); $iterator->next()) {
            if ($iterator->current() === '.' || $iterator->current() === '..') {
                continue;
            }

            if (is_dir($iterator->current()->getPathname())) {
                continue;
            }

            $list[] = $iterator->current()->getPathname();
        }
    } catch (Exception $ex) {
        throw $ex;
    }
    return $list;
}

/**
 * Padroniza todas os separadores de diretórios para o separador expresso em
 * DIRECTORY_SEPARATOR do PHP.
 * @param string $path Caminho para padronizar, passado por referência.
 * @return void
 */
function slash(string &$path): void
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
    $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
}

function endSlash(string &$path): void
{
    if (substr($path, -1, 1) !== '\\' && substr($path, -1, 1) !== '/') {
        $path .= DIRECTORY_SEPARATOR;
    }
}
