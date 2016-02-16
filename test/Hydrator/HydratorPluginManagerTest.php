<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zend-stdlib for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator;

use PHPUnit_Framework_TestCase as TestCase;
use ReflectionProperty;
use Zend\Hydrator\HydratorPluginManager as BasePluginManager;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\Hydrator\HydratorPluginManager;

class HydratorPluginManagerTest extends TestCase
{
    public function setUp()
    {
        $this->hydrators = new HydratorPluginManager();
    }

    public function testExtendsZendHydratorPluginManager()
    {
        $this->assertInstanceOf(BasePluginManager::class, $this->hydrators);
    }

    public function aliases()
    {
        $hydrators = new HydratorPluginManager();
        $r = new ReflectionProperty($hydrators, 'aliases');
        $r->setAccessible(true);
        foreach ($r->getValue($hydrators) as $alias => $target) {
            yield $alias => [$alias, $target];
        }
    }

    /**
     * @dataProvider aliases
     */
    public function testAllAliasesReturnStdlibEquivalents($alias, $expected)
    {
        $this->assertContains('\\Stdlib\\', $expected, 'Alias target is not a Stdlib class?');

        $hydrator = $this->hydrators->get($alias);
        $this->assertInstanceOf(
            $expected,
            $hydrator,
            sprintf('Alias %s did not retrieve expected %s instance; got %s', $alias, $expected, get_class($hydrator))
        );
        $this->assertInstanceOf(
            HydratorInterface::class,
            $hydrator,
            sprintf('Alias %s resolved to %s, which is not a HydratorInterface instance', $alias, get_class($hydrator))
        );
    }

    public function invokables()
    {
        $hydrators = new HydratorPluginManager();
        $r = new ReflectionProperty($hydrators, 'invokableClasses');
        $r->setAccessible(true);
        foreach ($r->getValue($hydrators) as $name => $target) {
            yield $name => [$name, $target];
        }
    }

    /**
     * @dataProvider invokables
     */
    public function testAllInvokablesReturnStdlibInstances($name, $expected)
    {
        $this->assertContains('\\Stdlib\\', $expected, 'Invokable target is not a Stdlib class?');

        $hydrator = $this->hydrators->get($name);
        $this->assertInstanceOf($expected, $hydrator, sprintf(
            'Invokable %s did not retrieve expected %s instance; got %s',
            $name,
            $expected,
            get_class($hydrator)
        ));
        $this->assertInstanceOf(HydratorInterface::class, $hydrator, sprintf(
            'Invokable %s resolved to %s, which is not a HydratorInterface instance',
            $name,
            get_class($hydrator)
        ));
    }

    public function factories()
    {
        $hydrators = new HydratorPluginManager();
        $r = new ReflectionProperty($hydrators, 'factories');
        $r->setAccessible(true);
        foreach ($r->getValue($hydrators) as $name => $factory) {
            yield $name => [$name, $factory];
        }
    }

    /**
     * @dataProvider factories
     */
    public function testAllFactoriesReturnStdlibInstances($name, $factory)
    {
        $hydrator = $this->hydrators->get($name);
        $this->assertInstanceOf(HydratorInterface::class, $hydrator, sprintf(
            'Factory for %s resolved to %s, which is not a HydratorInterface instance',
            $name,
            get_class($hydrator)
        ));
    }
}
