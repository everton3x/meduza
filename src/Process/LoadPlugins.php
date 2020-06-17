<?php

namespace Meduza\Process;

/**
 * Processa os plugins
 *
 * @author Everton
 */
class LoadPlugins
{
    protected \Meduza\Build\BuildRepo $buildRepo;
    
    protected \LogMan\Messenger\MessengerInterface $logger;


    public function __construct(\Meduza\Build\BuildRepo $buildRepo, \LogMan\Messenger\MessengerInterface $logger)
    {
        $this->buildRepo = $buildRepo;
        $this->logger = $logger;
    }
    
    public function run(): \Meduza\Build\BuildRepo
    {
        $plugDir = (new \PTK\FileSystem\Directory((new \PTK\FileSystem\Path($this->buildRepo->get('config.plugins.dir')))->slashes()->endSlash()))->realpath();
//        echo $plugDir, PHP_EOL;exit();
        $plugList = $this->buildRepo->get('config.plugins.active');
//        print_r($plugList);exit();
        
        foreach ($plugList as $plug) {
            $plugFile = "{$plugDir}{$plug}Plugin.php";
            $plugClass = "{$plug}Plugin";
            
            require $plugFile;
            
            $plugInstance = new $plugClass($this->buildRepo);
            
            $this->buildRepo = $plugInstance->run();
        }
        
        return $this->buildRepo;
    }
}
