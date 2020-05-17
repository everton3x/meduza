<?php
namespace Meduza\Test;

use Ds\Vector;
use Meduza\Build\Builder;
use PHPUnit\Framework\TestCase;

/**
 * Test for Builder
 *
 * @author everton
 */
class BuilderTest extends TestCase
{
    use TestTrait;
    
    /**
     * @dataProvider configProvider
     */
    public function testLoadContentDirSuccess($config)
    {
        
        $builder = new Builder($config);

        $this->assertInstanceOf(Vector::class, $this->invokeMethod($builder, 'loadContentDir', [$config->getAllconfig()['content_dir']]));
    }

}
