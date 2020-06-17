<?php

namespace Meduza\Process;

/**
 * Prepara meta-páginas.
 *
 * Meta-páginas nada mais é que uma lista com os dados principais das páginas
 * que serão utilizadas no processo de renderização do conteúdo final.
 *
 * Também é onde os plugins podem criar outras páginas, como páginas de tags,
 * categorias, etc.
 *
 * @author Everton
 */
class PrepareMetaPages implements ProcessInterface
{

    public function __construct()
    {
    }

    /**
     * Executa o processo.
     *
     * @param array<mixed> $buildData
     * @return array<mixed>
     */
    public function run(array $buildData): array
    {
        $contentDir = realpath($buildData['config']['content']['source']);
        slash($contentDir);
        endSlash($contentDir);
//        echo $contentDir, PHP_EOL;exit();
        $contentFiles = readFilesIntoDIr($contentDir);

        foreach ($contentFiles as $key => $file) {
            $buildData['metaPages'][$key]['fileSource'] = $file;
            $buildData['metaPages'][$key]['fileSourceDirectory'] = $this->getDirectory($file);
            $buildData['metaPages'][$key]['fileSourceExtension'] = $this->getExtension($file);
            $buildData['metaPages'][$key]['fileSourceBasename'] = $this->getBasename($file);
        }
        return $buildData;
    }
    
    /**
     * Pega o diretório do arquivo.
     *
     * @param string $file
     * @return string
     */
    protected function getDirectory(string $file): string
    {
        return dirname($file);
    }
    
    /**
     * Pega a extensão do arquivo.
     *
     * @param string $file
     * @return string
     */
    protected function getExtension(string $file): string
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }
    
    /**
     * Pega o nome do arquivo sem a extensão.
     *
     * @param string $file
     * @return string
     */
    protected function getBasename(string $file): string
    {
        return basename($file, ".{$this->getExtension($file)}");
    }
}
