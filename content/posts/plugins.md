---
title: "Como é o sistema de plugins"
published: "2020-04-30"
summary: "Como o Meduza utiliza plugins."
categories:
    - Meduza
    - Plugins
tags:
    - Meduza
    - plugins
---

Um plugin do Meduza nada mais é do que uma classe que implementa a interface 
```\Meduza\Plugin\PluginInterface```, cujo método ```PluginInterface::run()```
recebe um array com os dados da construção, modifica-os e devolve esse array com
as modificações feitas, que será a **entrada** para o plugim seguinte.

Um plugin é, também um processo. Meduza usa processos para as várias etapas de 
geração do conteúdo estático, e um plugim é só mais um processo.