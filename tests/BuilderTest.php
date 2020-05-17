<?php
namespace Meduza\Test;

use Ds\Vector;
use Meduza\Build\Builder;
use Meduza\Config\ConfigLoader;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Test for Builder
 *
 * @author everton
 */
class BuilderTest extends TestCase
{

    public function configProvider()
    {
        $loaderConfig = new ConfigLoader();
        $config = $loaderConfig->loadConfig('./meduza.yml');

        return [
            [$config]
        ];
    }

    /**
     * @dataProvider configProvider
     */
    public function testLoadContentDirSuccess($config)
    {
        
        $builder = new Builder($config);

        $this->assertInstanceOf(Vector::class, $this->invokeMethod($builder, 'loadContentDir', [$config->getAllconfig()['content_dir']]));
    }

    public function invokeMethod(&$object, string $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
