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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Stdlib;

/**
 * Serializable version of SplStack
 *
 * @category   Zend
 * @package    Zend_Stdlib
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SplStack extends \SplStack
{
    /**
     * @var array Used in serialization
     */
    private $_data = array();

    /**
     * Serialize to an array representing the stack
     * 
     * @return void
     */
    public function toArray()
    {
        $array = array();
        foreach ($this as $item) {
            $array[] = $item;
        }
        return $array;
    }

    /**
     * Serialize
     * 
     * @return array
     */
    public function __sleep()
    {
        $this->_data = $this->toArray();
        return array('_data');
    }

    /**
     * Unserialize
     * 
     * @return void
     */
    public function __wakeup()
    {
        foreach ($this->_data as $item) {
            $this->unshift($item);
        }
        $this->_data = array();
    }
}
