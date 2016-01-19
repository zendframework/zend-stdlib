CompositeNamingStrategy ======================

`Zend\Stdlib\Hydrator\NamingStrategy\CompositeNamingStrategy` allows you to specify which naming
strategy should be used for each key encountered during hydration or extraction.

# Basic Usage

When invoked, the following composite strategy will extract the property `bar` into array key `foo`
(MapNamingStrategy) and property `barBat` into `bar_bat` (UnderscoreNamingStrategy):

```php
class Foo
{
    public $bar;
    public $barBat;
}

$mapStrategy = new Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy([
    'foo' => 'bar'
]);

$underscoreNamingStrategy = new Zend\Stdlib\Hydrator\NamingStrategy\UnderscoreNamingStrategy();

$namingStrategy = new Zend\Stdlib\Hydrator\NamingStrategy\CompositeNamingStrategy([
    'bar' => $mapStrategy,
    'barBat' => $underscoreNamingStrategy,
]);

$hydrator = new Zend\Stdlib\Hydrator\ObjectProperty();
$hydrator->setNamingStrategy($namingStrategy);

$foo = new Foo();
$foo->bar = 123;
$foo->barBat = 42;

print_r($foo); // Foo Object ( [bar] => 123 [barBat] => 42 )
print_r($hydrator->extract($foo)); // Array ( [foo] => 123 [bar_bat] => 42 ) 
```

Unfortunately, this CompositeNamingStrategy can only be used for extraction as it will not know how
to handle the keys necessary for hydration (`foo` and `bar_bat`, respectively). To rectify this we
have to cover the keys for both hydration and extraction in our composite strategy:

```php
class Foo
{
    public $bar;
    public $barBat;
}

$mapStrategy = new Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy([
    'foo' => 'bar'
]);

$underscoreNamingStrategy = new Zend\Stdlib\Hydrator\NamingStrategy\UnderscoreNamingStrategy();

$namingStrategy = new Zend\Stdlib\Hydrator\NamingStrategy\CompositeNamingStrategy([
    // Define both directions for the foo => bar mapping
    'bar' => $mapStrategy,
    'foo' => $mapStrategy,
    // Define both directions for the barBat => bar_bat mapping
    'barBat' => $underscoreNamingStrategy,
    'bar_bat' => $underscoreNamingStrategy,
]);

$hydrator = new Zend\Stdlib\Hydrator\ObjectProperty();
$hydrator->setNamingStrategy($namingStrategy);

$foo = new Foo();
$foo->bar = 123;
$foo->barBat = 42;

$array = $hydrator->extract($foo);

print_r($foo); // Foo Object ( [bar] => 123 [barBat] => 42 )
print_r($array); // Array ( [foo] => 123 [bar_bat] => 42 ) 

$foo2 = new Foo();
$hydrator->hydrate($array, $foo2);

print_r($foo2); // Foo Object ( [bar] => 123 [barBat] => 42 )
```
