<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator\NamingStrategy;

use Zend\Stdlib\Hydrator\NamingStrategy\ArrayMapNamingStrategy;

class ArrayMapNamingStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSameNameWithEmptyMap()
    {
        $strategy = new ArrayMapNamingStrategy();
        $this->assertEquals('some_stuff', $strategy->hydrate('some_stuff'));
        $this->assertEquals('some_stuff', $strategy->extract('some_stuff'));
    }

    public function testGetNameWithMap()
    {
        $strategy = new ArrayMapNamingStrategy(array('stuff3' => 'stuff4'), array('stuff1' => 'stuff2'));
        $this->assertEquals('stuff2', $strategy->hydrate('stuff1'));
        $this->assertEquals('stuff4', $strategy->extract('stuff3'));        
    }
}
