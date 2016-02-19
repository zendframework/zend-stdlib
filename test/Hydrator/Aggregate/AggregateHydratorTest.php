<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator\Aggregate;

use PHPUnit_Framework_TestCase;
use Prophecy\Argument;
use Zend\EventManager\EventManager;
use Zend\Hydrator\Aggregate\HydratorListener;
use Zend\Hydrator\HydratorInterface;
use Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator;
use Zend\Stdlib\Hydrator\Aggregate\ExtractEvent;
use Zend\Stdlib\Hydrator\Aggregate\HydrateEvent;
use stdClass;

/**
 * Unit tests for {@see \Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator}
 */
class AggregateHydratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator
     */
    protected $hydrator;

    /**
     * @var \Zend\EventManager\EventManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManager;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->eventManager = $this->getMock(EventManager::class);
        $this->hydrator     = new AggregateHydrator();

        $this->hydrator->setEventManager($this->eventManager);
    }

    /**
     * @covers \Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator::add
     */
    public function testAdd()
    {
        $hydrator = $this->prophesize(HydratorInterface::class)->reveal();

        $events = $this->prophesize(EventManager::class);

        $events->setIdentifiers(Argument::type('array'))->shouldBeCalled();

        $events->attach(
            HydrateEvent::EVENT_HYDRATE,
            Argument::that(function ($argument) {
                if (! is_callable($argument)) {
                    return false;
                }
                if (! is_array($argument)) {
                    return false;
                }
                return (
                    $argument[0] instanceof HydratorListener
                    && $argument[1] === 'onHydrate'
                );
            }),
            123
        )->shouldBeCalled();

        $events->attach(
            ExtractEvent::EVENT_EXTRACT,
            Argument::that(function ($argument) {
                if (! is_callable($argument)) {
                    return false;
                }
                if (! is_array($argument)) {
                    return false;
                }
                return (
                    $argument[0] instanceof HydratorListener
                    && $argument[1] === 'onExtract'
                );
            }),
            123
        )->shouldBeCalled();

        $this->hydrator->setEventManager($events->reveal());
        $this->hydrator->add($hydrator, 123);
    }

    /**
     * @covers \Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator::hydrate
     */
    public function testHydrate()
    {
        $object = new stdClass();

        $this
            ->eventManager
            ->expects($this->once())
            ->method('triggerEvent')
            ->with($this->isInstanceOf('Zend\Hydrator\Aggregate\HydrateEvent'));

        $this->assertSame($object, $this->hydrator->hydrate(['foo' => 'bar'], $object));
    }

    /**
     * @covers \Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator::extract
     */
    public function testExtract()
    {
        $object = new stdClass();

        $this
            ->eventManager
            ->expects($this->once())
            ->method('triggerEvent')
            ->with($this->isInstanceOf('Zend\Hydrator\Aggregate\ExtractEvent'));

        $this->assertSame([], $this->hydrator->extract($object));
    }

    /**
     * @group 55
     * @covers \Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator::hydrate
     */
    public function testHydrateUsesStdlibHydrateEvent()
    {
        $object = new stdClass();

        $this
            ->eventManager
            ->expects($this->once())
            ->method('triggerEvent')
            ->with($this->isInstanceOf('Zend\Stdlib\Hydrator\Aggregate\HydrateEvent'));

        $this->assertSame($object, $this->hydrator->hydrate(['foo' => 'bar'], $object));
    }

    /**
     * @group 55
     * @covers \Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator::extract
     */
    public function testExtractUsesStdlibExtractEvent()
    {
        $object = new stdClass();

        $this
            ->eventManager
            ->expects($this->once())
            ->method('triggerEvent')
            ->with($this->isInstanceOf('Zend\Stdlib\Hydrator\Aggregate\ExtractEvent'));

        $this->assertSame([], $this->hydrator->extract($object));
    }

    /**
     * @covers \Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator::getEventManager
     * @covers \Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator::setEventManager
     */
    public function testGetSetManager()
    {
        $hydrator     = new AggregateHydrator();
        $eventManager = $this->getMock('Zend\EventManager\EventManagerInterface');

        $this->assertInstanceOf('Zend\EventManager\EventManagerInterface', $hydrator->getEventManager());

        $eventManager
            ->expects($this->once())
            ->method('setIdentifiers')
            ->with(
                [
                     'Zend\Hydrator\Aggregate\AggregateHydrator',
                     'Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator',
                ]
            );

        $hydrator->setEventManager($eventManager);

        $this->assertSame($eventManager, $hydrator->getEventManager());
    }
}
