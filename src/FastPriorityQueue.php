<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib;

use Iterator;
use Countable;
use Serializable;

/**
 * This is an efficient implementation of an integer priority queue in PHP
 *
 * This class acts like a queue with insert() and extract(), removing the
 * elements from the queue and it also acts like an Iterator without removing
 * the elements. This behaviour can be used in mixed scenarios with high
 * performance boost.
 */
class FastPriorityQueue implements Iterator, Countable, Serializable
{
    const EXTR_DATA     = 0x00000001;
    const EXTR_PRIORITY = 0x00000002;
    const EXTR_BOTH     = 0x00000003;

    /**
     * @var integer
     */
    protected $extractFlag = self::EXTR_DATA;

    /**
     * Elements of the queue, divided by priorities
     *
     * @var array
     */
    protected $values = [];

    /**
     * Array of priorities
     *
     * @var array
     */
    protected $priorities = [];

    /**
     * Array of priorities used for the iteration
     *
     * @var array
     */
    protected $subPriorities = [];

    /**
     * Max priority
     *
     * @var integer
     */
    protected $max = 0;

    /**
     * Total number of elements in the queue
     *
     * @var integer
     */
    protected $tot = 0;

    /**
     * Index of the current element in the queue
     *
     * @var integer
     */
    protected $index = 0;

    /**
     * Insert an element in the queue with a specified priority
     *
     * @param mixed $value
     * @param integer $priority a positive integer
     */
    public function insert($value, $priority)
    {
        if (!is_int($priority)) {
            throw new Exception\InvalidArgumentException("The priority must be an integer");
        }
        $this->values[$priority][] = $value;
        if (!isset($this->priorities[$priority])) {
            $this->priorities[$priority] = $priority;
            $this->max = max($priority, $this->max);
        }
        ++$this->tot;
    }

    /**
     * Extract an element in the queue according to the priority and the
     * order of insertion
     *
     * @return mixed
     */
    public function extract()
    {
        if (!$this->valid()) {
            return false;
        }
        $value = $this->current();
        $this->nextAndRemove();
        return $value;
    }

    /**
     * Get the total number of elements in the queue
     *
     * @return integer
     */
    public function count()
    {
        return $this->tot;
    }

    /**
     * Get the current element in the queue
     *
     * @return mixed
     */
    public function current()
    {
        switch ($this->extractFlag) {
            case self::EXTR_DATA:
                return current($this->values[$this->max]);
            case self::EXTR_PRIORITY:
                return $this->max;
            case self::EXTR_BOTH:
                return [
                    'data' => current($this->values[$this->max]),
                    'priority' => $this->max
                ];
        }
    }

    /**
     * Get the index of the current element in the queue
     *
     * @return integer
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * Set the iterator pointer to the next element in the queue
     * removing the previous element
     */
    protected function nextAndRemove()
    {
        if (false === next($this->values[$this->max])) {
            unset($this->priorities[$this->max]);
            unset($this->values[$this->max]);
            $this->max = empty($this->priorities) ? 0 : max($this->priorities);
        }
        ++$this->index;
        --$this->tot;
    }

    /**
     * Set the iterator pointer to the next element in the queue
     * without removing the previous element
     */
    public function next()
    {
        if (false === next($this->values[$this->max])) {
            unset($this->subPriorities[$this->max]);
            $this->max = empty($this->subPriorities) ? 0 : max($this->subPriorities);
        }
        ++$this->index;
        --$this->tot;
    }

    /**
     * Check if the current iterator is valid
     *
     * @return boolean
     */
    public function valid()
    {
        return isset($this->values[$this->max]);
    }

    /**
     * Rewind the current iterator
     */
    public function rewind()
    {
        $this->subPriorities = $this->priorities;
    }

    /**
     * Serialize to an array
     *
     * Array will be priority => data pairs
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach (clone $this as $item) {
            $array[] = $item;
        }
        return $array;
    }

    /**
     * Serialize
     *
     * @return string
     */
    public function serialize()
    {
        $clone = clone $this;
        $clone->setExtractFlags(self::EXTR_BOTH);

        $data = [];
        foreach ($clone as $item) {
            $data[] = $item;
        }

        return serialize($data);
    }

    /**
     * Deserialize
     *
     * @param  string $data
     * @return void
     */
    public function unserialize($data)
    {
        foreach (unserialize($data) as $item) {
            $this->insert($item['data'], $item['priority']);
        }
    }

    /**
     * Set the extract flag
     *
     * @param integer $flag
     */
    public function setExtractFlags($flag)
    {
        switch ($flag) {
            case self::EXTR_DATA:
            case self::EXTR_PRIORITY:
            case self::EXTR_BOTH:
                $this->extractFlag = $flag;
                break;
            default:
                throw new Exception\InvalidArgumentException("The extract flag specified is not valid");
        }
    }

    /**
     * Check if the queue is empty
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->values);
    }

    /**
     * Does the queue contain the given datum?
     *
     * @param  mixed $datum
     * @return bool
     */
    public function contains($datum)
    {
        foreach ($this->values as $values) {
            if (in_array($datum, $values)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Does the queue have an item with the given priority?
     *
     * @param  int $priority
     * @return bool
     */
    public function hasPriority($priority)
    {
        return isset($this->values[$priority]);
    }
}
