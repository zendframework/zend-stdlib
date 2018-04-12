<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendBench\Stdlib;

use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;
use Zend\Stdlib\FastPriorityQueue;
use Zend\Stdlib\PriorityQueue;

/**
 * @Revs(1000)
 * @Iterations(10)
 * @Warmup(2)
 */
class RemovePriorityQueueBench
{
    public function __construct()
    {
        $this->fastPriorityQueue = new FastPriorityQueue();
        $this->priorityQueue     = new PriorityQueue();

        for ($i = 0; $i < 1000; $i += 1) {
            $priority = rand(1, 100);
            $this->fastPriorityQueue->insert('foo', $priority);
            $this->priorityQueue->insert('foo', $priority);
        }
    }

    public function benchRemovePriorityQueue()
    {
        $this->priorityQueue->remove('foo');
    }

    public function benchRemoveFastPriorityQueue()
    {
        $this->fastPriorityQueue->remove('foo');
    }
}
