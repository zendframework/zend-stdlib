<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator\Iterator;

use Iterator;
use Zend\Stdlib\Hydrator\HydratorInterface;

interface HydratingIteratorInterface extends Iterator
{
    /**
     * This sets the prototype of that will be hydrated.  This can be the name of the class or
     * the object itself.  The iterator will clone the object
     *
     * @param string|object $prototype
     */
    public function setPrototype($prototype);

    /**
     * Sets the hydrator to use for the prototype
     *
     * @param HydratorInterface $hydrator
     */
    public function setHydrator(HydratorInterface $hydrator);
}
