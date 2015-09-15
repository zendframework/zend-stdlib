<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib;

use Zend\Stdlib\FastPriorityQueue;

/**
 * @group      Zend_Stdlib
 */
class FastPriorityQueueTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->queue = new FastPriorityQueue();
        $this->insertDataQueue($this->queue);
        $this->expected = [
            'test1',
            'test2',
            'test3',
            'test4',
            'test5',
            'test6'
        ];
    }

    protected function getDataPriorityQueue()
    {
        return [
            'test3' => 2,
            'test5' => 1,
            'test1' => 5,
            'test2' => 3,
            'test4' => 2,
            'test6' => 1
        ];
    }

    protected function insertDataQueue($queue)
    {
        foreach ($this->getDataPriorityQueue() as $value => $priority) {
            $queue->insert($value, $priority);
        }
    }

    /**
     * Test the insert and extract operations for the queue
     * We test that extract() function remove the elements
     */
    public function testInsertExtract()
    {
        foreach ($this->expected as $value) {
            $this->assertEquals($value, $this->queue->extract());
        }
        // We check that the elements are removed from the queue
        $this->assertTrue($this->queue->isEmpty());
    }

    public function testIteratePreserveElements()
    {
        $i = 0;
        foreach ($this->queue as $value) {
            $this->assertEquals($this->expected[$i++], $value);
        }
        // We check that the elements still exist in the queue
        $i = 0;
        foreach ($this->queue as $value) {
            $this->assertEquals($this->expected[$i++], $value);
        }
    }

    public function testMaintainsInsertOrderForDataOfEqualPriority()
    {
        $queue = new FastPriorityQueue();
        $queue->insert('foo', 1000);
        $queue->insert('bar', 1000);
        $queue->insert('baz', 1000);
        $queue->insert('bat', 1000);

        $expected = ['foo', 'bar', 'baz', 'bat'];
        $test     = [];
        foreach ($queue as $datum) {
            $test[] = $datum;
        }
        $this->assertEquals($expected, $test);
    }

    public function testSerializationAndDeserializationShouldMaintainState()
    {
        $s = serialize($this->queue);
        $unserialized = unserialize($s);
        $count = count($this->queue);
        $this->assertSame($count, count($unserialized), 'Expected count ' . $count . '; received ' . count($unserialized));

        $expected = [];
        foreach ($this->queue as $item) {
            $expected[] = $item;
        }
        $test = [];
        foreach ($unserialized as $item) {
            $test[] = $item;
        }
        $this->assertSame($expected, $test, 'Expected: ' . var_export($expected, 1) . "\nReceived:" . var_export($test, 1));
    }

    public function testCanRetrieveQueueAsArray()
    {
        $test = $this->queue->toArray();
        $this->assertSame($this->expected, $test, var_export($test, 1));
    }

    public function testIteratorFunctions()
    {
        $this->queue->rewind();

        $i = 0;
        while ($this->queue->valid()) {
            $key   = $this->queue->key();
            $value = $this->queue->current();
            $this->assertEquals($this->expected[$i], $value);
            $this->queue->next();
            ++$i;
        }
        $this->assertFalse($this->queue->valid());
    }

    public function testNoRewindOperation()
    {
        $this->assertEquals(0, $this->queue->key());
        $this->queue->next();
        $this->assertEquals(1, $this->queue->key());
        $this->queue->rewind();
        $this->assertEquals(1, $this->queue->key());
    }

    public function testSetExtractFlag()
    {
        $this->queue->setExtractFlags(FastPriorityQueue::EXTR_DATA);
        $this->assertEquals($this->expected[0], $this->queue->extract());
        $this->queue->setExtractFlags(FastPriorityQueue::EXTR_PRIORITY);
        $this->assertEquals(3, $this->queue->extract());
        $this->queue->setExtractFlags(FastPriorityQueue::EXTR_BOTH);
        $expected = [
            'data'     => $this->expected[2],
            'priority' => 2
        ];
        $this->assertEquals($expected, $this->queue->extract());
    }

    public function testIsEmpty()
    {
        $queue = new FastPriorityQueue();
        $this->assertTrue($queue->isEmpty());
        $queue->insert('foo', 1);
        $this->assertFalse($queue->isEmpty());
        $value = $queue->extract();
        $this->assertTrue($queue->isEmpty());
    }

    public function testContains()
    {
        foreach ($this->expected as $value) {
            $this->assertTrue($this->queue->contains($value));
        }
        $this->assertFalse($this->queue->contains('foo'));
    }

    public function testHasPriority()
    {
        foreach ($this->getDataPriorityQueue() as $value => $priority) {
            $this->assertTrue($this->queue->hasPriority($priority));
        }
        $this->assertFalse($this->queue->hasPriority(10000));
    }
}
