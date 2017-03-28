<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib;

use PHPUnit\Framework\TestCase;
use Zend\Stdlib\SplQueue;

/**
 * @group      Zend_Stdlib
 */
class SplQueueTest extends TestCase
{
    /**
     * @var SplQueue
     */
    protected $queue;

    public function setUp()
    {
        $this->queue = new SplQueue();
        $this->queue->push('foo');
        $this->queue->push('bar');
        $this->queue->push('baz');
    }

    public function testSerializationAndDeserializationShouldMaintainState()
    {
        $s = serialize($this->queue);
        $unserialized = unserialize($s);
        $count = count($this->queue);
        $this->assertSame($count, count($unserialized));

        $expected = iterator_to_array($this->queue);
        $test = iterator_to_array($unserialized);
        $this->assertSame($expected, $test);
    }

    public function testCanRetrieveQueueAsArray()
    {
        $expected = ['foo', 'bar', 'baz'];
        $this->assertSame($expected, $this->queue->toArray());
    }
}
