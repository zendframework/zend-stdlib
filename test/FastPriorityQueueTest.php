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
use Zend\Stdlib\Exception\InvalidArgumentException;
use Zend\Stdlib\FastPriorityQueue;

/**
 * @group      Zend_Stdlib
 */
class FastPriorityQueueTest extends TestCase
{
    /**
     * @var FastPriorityQueue
     */
    protected $queue;

    /**
     * @var string[]
     */
    protected $expected;

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
            'test3' => -1,
            'test5' => -10,
            'test1' => 5,
            'test2' => 2,
            'test4' => -1,
            'test6' => -10
        ];
    }

    protected function insertDataQueue(FastPriorityQueue $queue)
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
        $this->assertSame(
            $count,
            count($unserialized),
            'Expected count ' . $count . '; received ' . count($unserialized)
        );

        $expected = [];
        foreach ($this->queue as $item) {
            $expected[] = $item;
        }
        $test = [];
        foreach ($unserialized as $item) {
            $test[] = $item;
        }
        $this->assertSame(
            $expected,
            $test,
            'Expected: ' . var_export($expected, 1) . "\nReceived:" . var_export($test, 1)
        );
    }

    public function testCanRetrieveQueueAsArray()
    {
        $test = $this->queue->toArray();
        $this->assertSame($this->expected, $test, var_export($test, 1));
    }

    public function testIteratorFunctions()
    {
        $this->assertEquals($this->expected, iterator_to_array($this->queue));
    }

    public function testRewindOperation()
    {
        $this->assertEquals(0, $this->queue->key());
        $this->queue->next();
        $this->assertEquals(1, $this->queue->key());
        $this->queue->rewind();
        $this->assertEquals(0, $this->queue->key());
    }

    public function testSetExtractFlag()
    {
        $priorities = $this->getDataPriorityQueue();
        $this->queue->setExtractFlags(FastPriorityQueue::EXTR_DATA);
        $this->assertEquals($this->expected[0], $this->queue->extract());
        $this->queue->setExtractFlags(FastPriorityQueue::EXTR_PRIORITY);
        $this->assertEquals($priorities[$this->expected[1]], $this->queue->extract());
        $this->queue->setExtractFlags(FastPriorityQueue::EXTR_BOTH);
        $expected = [
            'data'     => $this->expected[2],
            'priority' => $priorities[$this->expected[2]]
        ];
        $this->assertEquals($expected, $this->queue->extract());
    }

    public function testSetInvalidExtractFlag()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The extract flag specified is not valid');
        $this->queue->setExtractFlags('foo');
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

    public function testCanRemoveItemFromQueue()
    {
        $this->assertTrue($this->queue->remove('test5'));
        $tot = count($this->getDataPriorityQueue()) - 1;
        $this->assertEquals($this->queue->count(), $tot);
        $this->assertEquals(count($this->queue), $tot);
        $expected = ['test1', 'test2', 'test3', 'test4', 'test6'];
        $test = [];
        foreach ($this->queue as $item) {
            $test[] = $item;
        }
        $this->assertEquals($expected, $test);
    }

    public function testRemoveOnlyTheFirstOccurenceFromQueue()
    {
        $data = $this->getDataPriorityQueue();
        $this->queue->insert('test2', $data['test2']);
        $tot = count($this->getDataPriorityQueue()) + 1;
        $this->assertEquals($this->queue->count(), $tot);
        $this->assertEquals(count($this->queue), $tot);

        $expected = ['test1', 'test2', 'test2', 'test3', 'test4', 'test5', 'test6'];
        $test = [];
        foreach ($this->queue as $item) {
            $test[] = $item;
        }
        $this->assertEquals($expected, $test);

        $this->assertTrue($this->queue->remove('test2'));
        $this->assertEquals($this->queue->count(), $tot - 1);
        $this->assertEquals(count($this->queue), $tot - 1);
        $test = [];
        foreach ($this->queue as $item) {
            $test[] = $item;
        }
        $this->assertEquals($this->expected, $test);
    }

    public function testRewindShouldNotRaiseErrorWhenQueueIsEmpty()
    {
        $queue = new FastPriorityQueue();
        $this->assertTrue($queue->isEmpty());

        $queue->rewind();
    }

    public function testRemoveShouldFindItemEvenIfMultipleItemsAreInQueue()
    {
        $prototype = function ($e) {
        };

        $queue = new FastPriorityQueue();
        $this->assertTrue($queue->isEmpty());

        $listeners = [];
        for ($i = 0; $i < 5; $i += 1) {
            $listeners[] = $listener = clone $prototype;
            $queue->insert($listener, 1);
        }

        $remove   = array_rand(array_keys($listeners));
        $listener = $listeners[$remove];

        $this->assertTrue($queue->contains($listener));
        $this->assertTrue($queue->remove($listener));
        $this->assertFalse($queue->contains($listener));
    }

    public function testIterativelyRemovingItemsShouldRemoveAllItems()
    {
        $prototype = function ($e) {
        };

        $queue = new FastPriorityQueue();
        $this->assertTrue($queue->isEmpty());

        $listeners = [];
        for ($i = 0; $i < 5; $i += 1) {
            $listeners[] = $listener = clone $prototype;
            $queue->insert($listener, 1);
        }

        for ($i = 0; $i < 5; $i += 1) {
            $listener = $listeners[$i];
            $queue->remove($listener);
        }

        for ($i = 0; $i < 5; $i += 1) {
            $listener = $listeners[$i];
            $this->assertFalse($queue->contains($listener), sprintf('Listener %s remained in queue', $i));
        }
    }

    public function testRemoveShouldNotAffectExtract()
    {
        // Removing an element with low priority
        $queue = new FastPriorityQueue();
        $queue->insert('a1', 1);
        $queue->insert('a2', 1);
        $queue->insert('b', 2);
        $queue->remove('a1');
        $expected = ['b', 'a2'];
        $test = [];
        while ($value = $queue->extract()) {
            $test[] = $value;
        }
        $this->assertEquals($expected, $test);
        $this->assertTrue($queue->isEmpty());

        // Removing an element in the middle of a set of elements with the same priority
        $queue->insert('a1', 1);
        $queue->insert('a2', 1);
        $queue->insert('a3', 1);
        $queue->remove('a2');
        $expected = ['a1', 'a3'];
        $test = [];
        while ($value = $queue->extract()) {
            $test[] = $value;
        }
        $this->assertEquals($expected, $test);
        $this->assertTrue($queue->isEmpty());

        // Removing an element with high priority
        $queue->insert('a', 1);
        $queue->insert('b', 2);
        $queue->remove('b');
        $expected = ['a'];
        $test = [];
        while ($value = $queue->extract()) {
            $test[] = $value;
        }
        $this->assertEquals($expected, $test);
        $this->assertTrue($queue->isEmpty());
    }

    public function testZeroPriority()
    {
        $queue = new FastPriorityQueue();
        $queue->insert('a', 0);
        $queue->insert('b', 1);
        $expected = ['b', 'a'];
        $test = [];
        foreach ($queue as $value) {
            $test[] = $value;
        }
        $this->assertEquals($expected, $test);
    }
}
