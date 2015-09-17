<?php
namespace ZendBench\Stdlib;

use Athletic\AthleticEvent;

class PriorityQueue extends AthleticEvent
{
    public function classSetUp()
    {
        $this->splPriorityQueue  = new \Zend\Stdlib\SplPriorityQueue();
        $this->fastPriorityQueue = new \Zend\Stdlib\FastPriorityQueue();
        $this->priorityQueue     = new \Zend\Stdlib\PriorityQueue();

        for($i=0; $i<5000; $i++) {
            $priority = rand(1,100);
            $this->splPriorityQueue->insert('foo', $priority);
            $this->fastPriorityQueue->insert('foo', $priority);
            $this->priorityQueue->insert('foo', $priority);
        }
    }

    /**
     * @iterations 5000
     */
    public function insertSplPriorityQueue()
    {
        $this->splPriorityQueue->insert('foo', rand(1,100));
    }

    /**
     * @iterations 5000
     */
    public function extractSplPriorityQueue()
    {
        $this->splPriorityQueue->extract();
    }

    /**
     * @iterations 5000
     */
    public function insertPriorityQueue()
    {
        $this->priorityQueue->insert('foo', rand(1,100));
    }

    /**
     * @iterations 5000
     */
    public function extractPriorityQueue()
    {
        $this->priorityQueue->extract();
    }
    
    /**
     * @iterations 5000
     */
    public function insertFastPriorityQueue()
    {
        $this->fastPriorityQueue->insert('foo', rand(1,100));
    }

    /**
     * @iterations 5000
     */
    public function extractFastPriorityQueue()
    {
        $this->fastPriorityQueue->extract();
    }
}
