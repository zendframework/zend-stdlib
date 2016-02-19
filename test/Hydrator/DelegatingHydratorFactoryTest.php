<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\DelegatingHydratorFactory;

class DelegatingHydratorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $hydratorManager = $this->prophesize(ServiceLocatorInterface::class);
        $hydratorManager->willImplement(ContainerInterface::class);

        $factory = new DelegatingHydratorFactory();
        $this->assertInstanceOf(
            'Zend\Hydrator\DelegatingHydrator',
            $factory->createService($hydratorManager->reveal())
        );
    }
}
