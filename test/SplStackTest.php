<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib;

use Zend\Stdlib\SplStack;

/**
 * @group      Zend_Stdlib
 */
class SplStackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SplStack
     */
    protected $stack;

    public function setUp()
    {
        $this->stack = new SplStack();
        $this->stack->push('foo');
        $this->stack->push('bar');
        $this->stack->push('baz');
        $this->stack->push('bat');
    }

    public function testSerializationAndDeserializationShouldMaintainState()
    {
        $s = serialize($this->stack);
        $unserialized = unserialize($s);
        $count = count($this->stack);
        $this->assertSame($count, count($unserialized));

        $expected = iterator_to_array($this->stack);
        $test = iterator_to_array($unserialized);
        $this->assertSame($expected, $test);
    }

    public function testCanRetrieveQueueAsArray()
    {
        $expected = ['bat', 'baz', 'bar', 'foo'];
        $test     = $this->stack->toArray();
        $this->assertSame($expected, $test, var_export($test, 1));
    }
}
