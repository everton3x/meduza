<?php

namespace Meduza\Test;

use PHPUnit\Framework\TestCase;

/**
 * Tests for configuration
 *
 * @author everton
 */
class ConfigTest extends TestCase {
    
    public function testConfigLoaderSuccess()
    {
        $configLoader = new \Meduza\Config\ConfigLoader();
        $configObj = $configLoader->loadConfig('./meduza.yml');
        
        $this->assertInstanceOf(\Meduza\Config\ConfigInterface::class, $configObj);
        
        $this->assertIsArray($configObj->getAllConfig());
    }
}
