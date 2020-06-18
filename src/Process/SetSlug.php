<?php

namespace Meduza\Process;

use Exception;

use function end_slash;
use function slash;

/**
 * Configura o slug das páginas.
 *
 * Slugo é o caminho completo para a página. Pode ser configurado no frontmatter
 * através da diretiva slug ou, se ausente, toma por base o caminho do arquivo
 * dentro do diretório de conteúdo.
 *
 * @author Everton
 */
class SetSlug implements ProcessInterface
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
            if (!isset($metaPages[$key]['frontmatter']['slug'])) {
                $metaPages[$key]['frontmatter']['slug'] = $this->getSlug(
                    $contentDir,
                    $page['fileSource'],
                    $page['fileSourceExtension'],
                );
            }

            $metaPages[$key]['frontmatter']['slug'] = $this->prependSiteUrl(
                $metaPages[$key]['frontmatter']['slug'],
                $buildData['config']['site']['url']
            );
        }
        return $buildData;
    }

    /**
     * Adiciona a url do site antes do slug.
     *
     * @param string $slug
     * @param string $siteUrl
     * @return string
     */
    protected function prependSiteUrl(string $slug, string $siteUrl): string
    {
        if (substr($siteUrl, -1, 1) !== '/') {
            $siteUrl .= '/';
        }

        return $siteUrl . $slug;
    }

    /**
     * Cria um slug.
     *
     * @param string $contentDir
     * @param string $file
     * @param string $extension
     * @return string
     */
    protected function getSlug(string $contentDir, string $file, string $extension): string
    {
        $slug = preg_replace([
            "#^$contentDir#",
            "#$extension$#"
            ], [
            '',
            'html'
            ], $file);

        if (is_null($slug)) {
            throw new Exception("Falha ao gerar slug para $file");
        }

        return $slug;
    }
}
