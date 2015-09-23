<?php
/**
 * @see       http://github.com/zendframework/zend-stdlib for the canonical source repository
 * @copyright Copyright (c) 2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-stdlib/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\Stdlib\TestAsset;

use Zend\Stdlib\Hydrator\HydratorInterface;

class DeprecatedInterfaceHydrator implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
    }

    /**
     * Extract values from an object
     *
     * @param  object $object
     * @return array
     */
    public function extract($object)
    {
    }
} 
