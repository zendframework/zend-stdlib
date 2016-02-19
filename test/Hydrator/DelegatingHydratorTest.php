<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator;

use ArrayObject;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\DelegatingHydrator;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Unit tests for {@see \Zend\Stdlib\Hydrator\DelegatingHydrator}
 *
 * @covers \Zend\Stdlib\Hydrator\DelegatingHydrator
 */
class DelegatingHydratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DelegatingHydrator
     */
    protected $hydrator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $hydrators;

    /**
     * @var ArrayObject
     */
    protected $object;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->hydrators = $this->prophesize(ServiceLocatorInterface::class);
        $this->hydrators->willImplement(ContainerInterface::class);
        $this->hydrator = new DelegatingHydrator($this->hydrators->reveal());
        $this->object = new ArrayObject;
    }

    public function testExtract()
    {
        $hydrator = $this->prophesize(HydratorInterface::class);
        $hydrator->extract($this->object)->willReturn(['foo' => 'bar']);

        $this->hydrators->has(ArrayObject::class)->willReturn(true);
        $this->hydrators->get(ArrayObject::class)->willReturn($hydrator->reveal());

        $this->assertEquals(['foo' => 'bar'], $this->hydrator->extract($this->object));
    }

    public function testHydrate()
    {
        $hydrator = $this->prophesize(HydratorInterface::class);
        $hydrator->hydrate(['foo' => 'bar'], $this->object)->willReturn($this->object);

        $this->hydrators->has(ArrayObject::class)->willReturn(true);
        $this->hydrators->get(ArrayObject::class)->willReturn($hydrator->reveal());

        $this->assertEquals($this->object, $this->hydrator->hydrate(['foo' => 'bar'], $this->object));
    }
}
