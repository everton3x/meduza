<?php
namespace Meduza\Test;

use Meduza\Config\ConfigLoader;
use ReflectionClass;

/**
 *
 * @author everton
 */
trait TestTrait
{
    
    public function configProvider()
    {
        $loaderConfig = new ConfigLoader();
        $config = $loaderConfig->loadConfig('./meduza.yml');

        return [
            [$config]
        ];
    }
    
    public function invokeMethod(&$object, string $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
