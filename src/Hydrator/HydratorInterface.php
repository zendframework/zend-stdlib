<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage Hydrator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Stdlib\Hydrator;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage Hydrator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface HydratorInterface
{
    /**
     * Extract values from an object
     * 
     * @param  object $object 
     * @return array
     */
    public function extract($object);

    /**
     * Hydrate $object with the provided $data.
     * 
     * @param  array $data 
     * @param  object $object 
     * @return void
     */
    public function hydrate(array $data, $object);
}
