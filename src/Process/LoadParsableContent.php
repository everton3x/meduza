<?php

namespace Meduza\Process;

use Exception;

/**
 * Carrega os o conteúdo parsable dos arquivos de conteúdo
 *
 * @author Everton
 */
class LoadParsableContent implements ProcessInterface
{

    public function __construct()
    {
    }

    public function run(array $buildData): array
    {
        $metaPages = &$buildData['metaPages'];

        foreach ($metaPages as $key => $page) {
            $file = $page['fileSource'];
            $metaPages[$key]['parsableContent'] = $this->getParsable($file);
        }
        return $buildData;
    }

    protected function getParsable(string $file): string
    {
        $handle = fopen($file, 'r');

        if ($handle === false) {
            throw new Exception("Falha ao tentar abrir $file");
        }

        $control = 0;
        $parsable = '';
        while (($line = fgets($handle)) != false) {
            if ($control >= 2) {
                $parsable .= $line;
            }
            if (trim($line) === '---') {
                $control++;
            }
        }

        return $parsable;
    }
}
