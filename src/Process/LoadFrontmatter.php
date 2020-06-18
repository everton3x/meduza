<?php

namespace Meduza\Process;

use Exception;

use function end_slash;
use function slash;

/**
 * Carrega os meta-dados (front matter) dos arquivos de conteúdo
 *
 * @author Everton
 */
class LoadFrontmatter implements ProcessInterface
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
        $contentDir = realpath($buildData['config']['content']['source']);
        if ($contentDir === false) {
            throw new Exception("Falha ao processar o caminho para {$buildData['config']['content']['source']}");
        }
        $contentDir = slash($contentDir);
        $contentDir = end_slash($contentDir);

        foreach ($metaPages as $key => $page) {
            $file = $page['fileSource'];
            $metaPages[$key]['frontmatter'] = $this->loadFrontmatter($file);

        }
        return $buildData;
    }

    /**
     *
     * @param string $file
     * @return array<mixed>
     */
    protected function loadFrontmatter(string $file): array
    {
        return yaml_parse_file($file, 0);
    }
}
