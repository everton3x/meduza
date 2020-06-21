#!/usr/bin/env php
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

require './bootstrap.php';

require 'vendor/autoload.php';

//escolhe qual ambiente a ser usado
$env = Meduza\Environment\Environment::DEVELOPMENT;
//$env = Meduza\Environment::PRODUTION;
//carrega as configurações
$configLoader = new Meduza\Config\Loader();
$config = $configLoader->load('meduza.yml', $env);
//print_r($config);
//configurando o builder

$builder = new \Meduza\Build\Builder($config);

$builder->registerProcess(new Meduza\Process\PrepareMetaPages())
    ->registerProcess(new Meduza\Process\LoadFrontmatter())
    ->registerProcess(new Meduza\Process\LoadParsableContent())
    ->registerProcess(new Meduza\Process\SetSlug())
        //desativado pois é o autor do conteúdo que deve fazer o controle de dadas
        //também vai possibilitar implementar a geração de conteúdo apenas para os arquivos que tem published
//    ->registerProcess(new Meduza\Process\SetFrontmatterDates())
    ->registerProcess(new Meduza\Process\LoadPlugins())
    ->registerProcess(new Meduza\Process\ParseToHTML())
    ->registerProcess(new Meduza\Process\MergeTemplate())
    ->registerProcess(new Meduza\Process\PrepareTarget())
    ->registerProcess(new Meduza\Process\CopyThemeStaticContent())
    ->registerProcess(new Meduza\Process\CopySiteStaticContent())
    ->registerProcess(new Meduza\Process\SaveOutput())
;

$builder->build();
