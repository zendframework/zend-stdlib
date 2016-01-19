# Zend\\Stdlib\\Hydrator\\Strategy

You can add a `Zend\Stdlib\Hydrator\Strategy\StrategyInterface` to any of the hydrators (expect it
extends `Zend\Stdlib\Hydrator\AbstractHydrator` or implements
`Zend\Stdlib\Hydrator\HydratorInterface` and
`Zend\Stdlib\Hydrator\Strategy\StrategyEnabledInterface`) to manipulate the way how they behave on
`extract()` and `hydrate()` for specific key / value pairs. This is the interface that needs to be
implemented:

```php
namespace Zend\Stdlib\Hydrator\Strategy;

interface StrategyInterface
{
     /**
      * Converts the given value so that it can be extracted by the hydrator.
      *
      * @param mixed $value The original value.
      * @return mixed Returns the value that should be extracted.
      */
     public function extract($value);
     /**
      * Converts the given value so that it can be hydrated by the hydrator.
      *
      * @param mixed $value The original value.
      * @return mixed Returns the value that should be hydrated.
      */
     public function hydrate($value);
}
```

As you can see, this interface is similar to `Zend\Stdlib\Hydrator\HydratorInterface`. The reason
why is, that the strategies provide a proxy implementation for `hydrate()` and `extract()`.

## Adding strategies to the hydrators

To allow strategies within your hydrator, the
`Zend\Stdlib\Hydrator\Strategy\StrategyEnabledInterface` provide the following methods:

```php
namespace Zend\Stdlib\Hydrator;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

interface StrategyEnabledInterface
{
    /**
     * Adds the given strategy under the given name.
     *
     * @param string $name The name of the strategy to register.
     * @param StrategyInterface $strategy The strategy to register.
     * @return HydratorInterface
     */
    public function addStrategy($name, StrategyInterface $strategy);

    /**
     * Gets the strategy with the given name.
     *
     * @param string $name The name of the strategy to get.
     * @return StrategyInterface
     */
    public function getStrategy($name);

    /**
     * Checks if the strategy with the given name exists.
     *
     * @param string $name The name of the strategy to check for.
     * @return bool
     */
    public function hasStrategy($name);

    /**
     * Removes the strategy with the given name.
     *
     * @param string $name The name of the strategy to remove.
     * @return HydratorInterface
     */
    public function removeStrategy($name);
}
```

Every hydrator, that is shipped by default, provides this functionality. The `AbstractHydrator` has
it fully functional implemented. If you want to use this functionality in your own hydrators, you
should extend the `AbstractHydrator`.

## Available implementations

- **Zend\\Stdlib\\Hydrator\\Strategy\\SerializableStrategy**

    This is a strategy that provides the functionality for `Zend\Stdlib\Hydrator\ArraySerializable`.
You can use it with custom implementations for `Zend\Serializer\Adapter\AdapterInterface` if you
want to.

- **Zend\\Stdlib\\Hydrator\\Strategy\\ClosureStrategy**

    This is a strategy that allows you to pass in a `hydrate` callback to be called in the event of
hydration, and an `extract` callback to be called in the event of extraction.

- **Zend\\Stdlib\\Hydrator\\Strategy\\DefaultStrategy**

    This is a kind of dummy-implementation, that simply proxies everything through, without doing
anything on the parameters.

## Writing custom strategies

As usual, this is not really a very useful example, but will give you a good point about how to
start with writing your own strategies and where to use them. This strategy simply transform the
value for the defined key to rot13 on `extract()` and back on `hydrate()`:

```php
class Rot13Strategy implements StrategyInterface
{
    public function extract($value)
    {
        return str_rot13($value);
    }

    public function hydrate($value)
    {
        return str_rot13($value);
    }
}
```

This is the example class, we want to use for the hydrator example:

```php
class Foo
{
    protected $foo = null;
    protected $bar = null;

    public function getFoo()
    {
        return $this->foo;
    }

    public function setFoo($foo)
    {
        $this->foo = $foo;
    }

    public function getBar()
    {
        return $this->bar;
    }

    public function setBar($bar)
    {
        $this->bar = $bar;
    }
}
```

Now, we want to add the rot13 strategy to the method `getFoo()` and `setFoo($foo)`:

```php
$foo = new Foo();
$foo->setFoo("bar");
$foo->setBar("foo");

$hydrator = new ClassMethods();
$hydrator->addStrategy("foo", new Rot13Strategy());
```

When you now use the hydrator, to get an array of the object $foo, this is the array you'll get:

```php
$extractedArray = $hydrator->extract($foo);

// array(2) {
//     ["foo"]=>
//     string(3) "one"
//     ["bar"]=>
//     string(3) "foo"
// }
```

And the the way back:

```php
$hydrator->hydrate($extractedArray, $foo)

// object(Foo)#2 (2) {
//   ["foo":protected]=>
//   string(3) "bar"
//   ["bar":protected]=>
//   string(3) "foo"
// }
```
