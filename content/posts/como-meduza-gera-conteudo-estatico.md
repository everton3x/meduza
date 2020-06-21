---
title: "Como Meduza gera o conteúdo estático?"
published: "2020-04-29"
summary: "Como o conteúdo estático é gerado pelo Meduza através da organziação em processos."
categories:
    - Meduza
    - Processos
tags:
    - meduza
    - como funciona
---

A geração de conteúdo pelo Meduza é baseada em processos.

A classe ```\Meduza\Build\Builder``` nada mais é do que um **maestro** que vai 
chamando cada processo na ordem em que foram registrados.

Cada processo deve implementar ```\Meduza\Process\ProcessInterface``` e possuir
um método ```\Meduza\Process\ProcessInterface::run()``` que irá receber um array
com os dados da construção, acrescentando dados a ele, alterando-o ou realizando
alguma ação com base no que estiver nesse array especial.

Esse método também deve retornar esse array ao final do processamento. Assim, a 
saída de um processo alimentará o processo seguinte até a finalização da geração
do conteúdo estático.
