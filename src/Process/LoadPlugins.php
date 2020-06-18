<?php

namespace Meduza\Process;

use Exception;

use function end_slash;

/**
 * Processa os plugins
 *
 * @author Everton
 */
class LoadPlugins implements ProcessInterface
{

    public function __construct()
    {
    }

    public function run(array $buildData): array
    {
        $plugDir = realpath($buildData['config']['plugins']['dir']);
        if ($plugDir === false) {
            throw new Exception("Falha ao pegar o caminho dos plugins em {$buildData['config']['plugins']['dir']}.");
        }
        $plugDir = end_slash($plugDir);
//        echo $plugDir,PHP_EOL;exit;
        $plugList = $buildData['config']['plugins']['active'];
//        print_r($plugList);exit;
        foreach ($plugList as $plug) {
//            $plugFile = "{$plugDir}{$plug}Plugin.php";
//            echo $plugFile, PHP_EOL;
            $plugClass = "\\Meduza\\Plugin\\{$plug}Plugin";
//            echo "$plugClass", PHP_EOL;exit;

//            require $plugFile;

            $plugInstance = new $plugClass();

            $buildData = $plugInstance->run($buildData);
        }

        return $buildData;
    }
}
