<?php
/**
 * @see       http://github.com/zendframework/zend-stdlib for the canonical source repository
 * @copyright Copyright (c) 2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-stdlib/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\Stdlib\TestAsset;

use Zend\Hydrator\HydratorInterface;

/**
 * This test asset exists to see how deprecation works; it is associated with
 * the test ZendTest\Stdlib\HydratorDeprecationTest.
 */
class HydratorInjectedObjectUsingDeprecatedInterfaceTypehint
{
    public $hydrator;

    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
    }
}
