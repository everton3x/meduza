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
namespace Meduza\Build;

/**
 * Builder do conteúdo estático.
 *
 * Uma instância Builder é como um maestro para o processo de construção do
 * conteúdo estático.
 *
 * O processo de construção começa com as configurações e vai passando por
 * diversos processos, que correspondem a cada uma das estapas necessárias para
 * a construção do código estático do site.
 *
 * @author everton
 */
class Builder
{

    /**
     *
     * @var array<mixed> As configurações do projeto.
     */
    protected array $config;

    /**
     *
     * @var array<\Meduza\Process\ProcessInterface> A lista de processos (instâncias que implementam
     * Meduza\Process\ProcessInterface) a serem executados.
     */
    protected array $process;

    /**
     *
     * @param array<mixed> $config As configurações obtidas com Meduza\Config\Loader
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Registra um processo (instância de \Meduza\Process\ProcessInterface para
     * execução.
     *
     * @param \Meduza\Process\ProcessInterface $process
     * @return \Meduza\Build\Builder
     */
    public function registerProcess(\Meduza\Process\ProcessInterface $process): Builder
    {
        $this->process[] = $process;
        return $this;
    }

    /**
     * Executa o processo de construção.
     * @return void
     */
    public function build(): void
    {
        $buildData = [
            'config' => $this->config
        ];
        foreach ($this->process as $process) {
            $buildData = $process->run($buildData);
        }
//        print_r($buildData);
    }
}
