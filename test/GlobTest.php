<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Stdlib\Glob;

class GlobTest extends TestCase
{
    public function testFallback()
    {
        if (!defined('GLOB_BRACE')) {
            $this->markTestSkipped('GLOB_BRACE not available');
        }

        $this->assertEquals(
            glob(__DIR__ . '/_files/{alph,bet}a', GLOB_BRACE),
            Glob::glob(__DIR__ . '/_files/{alph,bet}a', Glob::GLOB_BRACE, true)
        );
    }

    public function testNonMatchingGlobReturnsArray()
    {
        $result = Glob::glob('/some/path/{,*.}{this,orthis}.php', Glob::GLOB_BRACE);
        $this->assertInternalType('array', $result);
    }

    public function testThrowExceptionOnError()
    {
        $this->setExpectedException('Zend\Stdlib\Exception\RuntimeException');

        // run into a max path lengh error
        $path = '/' . str_repeat('a', 10000);
        Glob::glob($path);
    }
}
