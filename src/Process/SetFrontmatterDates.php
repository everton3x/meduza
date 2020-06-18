<?php

namespace Meduza\Process;

use Exception;

/**
 * Faz um tratamento das datas no frontmatter.
 *
 * Basicamente, se não existir published e modified, ele usa a data da última
 * modificação do arquivo.
 * Útil para plugins de ordenação por data, por exemplo.
 *
 * @author Everton
 */
class SetFrontmatterDates implements ProcessInterface
{

    public function __construct()
    {
    }

    /**
     *
     * @param array<mixed> $buildData
     * @return array<mixed>
     */
    public function run(array $buildData): array
    {
        $metaPages = &$buildData['metaPages'];

        foreach ($metaPages as $key => $page) {
                    $filePath = $page['fileSource'];
            if (!file_exists($filePath)) {
                throw new Exception("Arquivo $filePath não encontrado.");
            }
            if (!isset($page['frontmatter']['modified'])) {
                $timestamp = filemtime($filePath);
                if ($timestamp === false) {
                    throw new Exception("Falha ao detectar a data de modificação de $filePath");
                }
                $metaPages[$key]['frontmatter']['modified'] = date('Y-m-d H:i:s', $timestamp);
            }
            
            if (!isset($page['frontmatter']['published'])) {
                $metaPages[$key]['frontmatter']['published'] = $metaPages[$key]['frontmatter']['modified'];
            }
        }
        return $buildData;
    }
}
