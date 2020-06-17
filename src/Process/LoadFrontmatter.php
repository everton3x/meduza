<?php

namespace Meduza\Process;

use Exception;

use function endSlash;
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
        slash($contentDir);
        endSlash($contentDir);

        foreach ($metaPages as $key => $page) {
            $file = $page['fileSource'];
            $metaPages[$key]['frontmatter'] = $this->loadFrontmatter($file);

            if (!isset($metaPages[$key]['frontmatter']['slug'])) {
                $metaPages[$key]['frontmatter']['slug'] = $this->getSlug(
                    $contentDir,
                    $page['fileSource'],
                    $page['fileSourceExtension'],
                );
            }

            $this->prependSiteUrl($metaPages[$key]['frontmatter']['slug'], $buildData['config']['site']['url']);
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
    protected function prependSiteUrl(string &$slug, string $siteUrl): string
    {
        if (substr($siteUrl, -1, 1) !== '/') {
            $siteUrl .= '/';
        }

        return $slug = $siteUrl . $slug;
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
