# Zend\\Stdlib\\Hydrator

Hydration is the act of populating an object from a set of data.

The `Hydrator` is a simple component to provide mechanisms both for hydrating objects, as well as
extracting data sets from them.

The component consists of an interface, and several implementations for common use cases.

## HydratorInterface

```php
namespace Zend\Stdlib\Hydrator;

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
     * @return object
     */
    public function hydrate(array $data, $object);
}
```

## Usage

Usage is quite simple: simply instantiate the hydrator, and then pass information to it.

```php
use Zend\Stdlib\Hydrator;
$hydrator = new Hydrator\ArraySerializable();

$object = new ArrayObject(array());

$hydrator->hydrate($someData, $object);

// or, if the object has data we want as an array:
$data = $hydrator->extract($object);
```

## Available Implementations

- **Zend\\Stdlib\\Hydrator\\ArraySerializable**

    Follows the definition of `ArrayObject`. Objects must implement either the `exchangeArray()` or
`populate()` methods to support hydration, and the `getArrayCopy()` method to support extraction.

- **Zend\\Stdlib\\Hydrator\\ClassMethods**

    Any data key matching a setter method will be called in order to hydrate; any method matching a
getter method will be called for extraction.

- **Zend\\Stdlib\\Hydrator\\DelegatingHydrator**

    Composes a hydrator locator containing hydrators and will delegate `hydrate()` and `extract()`
calls to the appropriate one based upon the class name of the object being operated on.

```php
// Instantiate each hydrator you wish to delegate to
$albumHydrator = new Zend\Stdlib\Hydrator\ClassMethods;
$artistHydrator = new Zend\Stdlib\Hydrator\ClassMethods;

// Map the entity class name to the hydrator using the HydratorPluginManager
// In this case we have two entity classes, "Album" and "Artist"
$hydrators = new Zend\Stdlib\Hydrator\HydratorPluginManager;
$hydrators->setService('Album', $albumHydrator);
$hydrators->setService('Artist', $artistHydrator);

// Create the DelegatingHydrator and tell it to use our configured hydrator locator
$delegating = new Zend\Stdlib\Hydrator\DelegatingHydrator($hydrators);

// Now we can use $delegating to hydrate or extract any supported object    
$array = $delegating->extract(new Artist);
$artist = $delegating->hydrate($data, new Artist);
```

- **Zend\\Stdlib\\Hydrator\\ObjectProperty**

    Any data key matching a publicly accessible property will be hydrated; any public properties
will be used for extraction.

- **Zend\\Stdlib\\Hydrator\\Reflection**

    Similar to the `ObjectProperty` hydrator, but uses [PHP's reflection
API](http://php.net/manual/en/intro.reflection.php) to hydrate or extract properties of any
visibility. Any data key matching an existing property will be hydrated; any existing properties
will be used for extraction.


