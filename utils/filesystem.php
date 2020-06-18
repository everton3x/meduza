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
function read_files_from_directory(string $dir): array
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
 * @param string $path Caminho para padronizar.
 * @return string
 */
function slash(string $path): string
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
    $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
    return $path;
}

/**
 * Adiciona DIRECTORY_SEPARATOR ao final de um caminho de diretório.
 * @param string $path
 * @return string
 */
function end_slash(string $path): string
{
    if (substr($path, -1, 1) !== '\\' && substr($path, -1, 1) !== '/') {
        $path .= DIRECTORY_SEPARATOR;
    }

    return $path;
}

/**
 * Apaga todo o conteúdo do diretório em $path de forma recursiva.
 *
 * @param string $path
 * @param string $exclude Regex para ignorar na exclusão. Usa preg_match para checagem.
 * @return void
 */
function clear_directory_content(string $path, string $exclude = ''): void
{
    if (is_dir($path) === false) {
        throw new Exception("$path não é um diretório ou não existe.");
    }
    $path = end_slash($path);
    $dir = opendir($path);
    if ($dir === false) {
        throw new Exception("Não foi possível abrir $path.");
    }

    while (false !== ($entry = readdir($dir))) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        $entry = $path . $entry;

        if (preg_match("/$exclude/", $entry) == true) {
            continue;
        }

        if (is_dir($entry)) {
            clear_directory_content($entry, $exclude);
            if (rmdir($entry) === false) {
                throw new Exception("Não foi possível excluir $entry");
            }
            continue;
        }

        if (unlink($entry) === false) {
            throw new Exception("Não foi possível excluir $entry");
        }

//        echo $entry, PHP_EOL;
    }
    closedir($dir);
}
