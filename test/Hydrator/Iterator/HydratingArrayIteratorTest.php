<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Hydrator\Iterator;

use ArrayObject;
use Zend\Stdlib\Hydrator\ArraySerializable;
use Zend\Stdlib\Hydrator\Iterator\HydratingArrayIterator;

class HydratingArrayIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testHydratesObjectAndClonesOnCurrent()
    {
        $data = [
            ['foo' => 'bar'],
            ['baz' => 'bat'],
        ];

        $object   = new ArrayObject();

        $hydratingIterator = new HydratingArrayIterator(new ArraySerializable(), $data, $object);

        $hydratingIterator->rewind();
        $this->assertEquals(new ArrayObject($data[0]), $hydratingIterator->current());
        $this->assertNotSame(
            $object,
            $hydratingIterator->current(),
            'Hydrating Iterator did not clone the object'
        );

        $hydratingIterator->next();
        $this->assertEquals(new ArrayObject($data[1]), $hydratingIterator->current());
    }

    public function testUsingStringForObjectName()
    {
        $data = [
            ['foo' => 'bar'],
        ];

        $hydratingIterator = new HydratingArrayIterator(new ArraySerializable(), $data, '\ArrayObject');

        $hydratingIterator->rewind();
        $this->assertEquals(new ArrayObject($data[0]), $hydratingIterator->current());
    }

    public function testThrowingInvalidArguementExceptionWhenSettingPrototypeToInvalidClass()
    {
        $this->setExpectedException('Zend\Hydrator\Exception\InvalidArgumentException');
        $hydratingIterator = new HydratingArrayIterator(new ArraySerializable(), [], 'not a real class');
    }
}
