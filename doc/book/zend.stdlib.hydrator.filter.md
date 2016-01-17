# Zend\\Stdlib\\Hydrator\\Filter

The hydrator filters, allows you to manipulate the behavior, when you want to `extract()` your stuff
to arrays. This is especially useful, if you want to `extract()` your objects to the userland and
strip some internals (e.g. `getServiceManager()`).

It comes with a helpful Composite Implementation and a few filters for common use cases. The filters
are implemented on the `AbstractHydrator`, so you can directly start using them if you extend it -
even on custom hydrators.

```php
namespace Zend\Stdlib\Hydrator\Filter;

interface FilterInterface
{
    /**
     * Should return true, if the given filter
     * does not match
     *
     * @param string $property The name of the property
     * @return bool
     */
    public function filter($property);
}
```

If it returns true, the key / value pairs will be in the extracted arrays - if it will return false,
you'll not see them again.

## Filter implementations

- **Zend\\Stdlib\\Hydrator\\Filter\\GetFilter**

    This filter is used in the `ClassMethods` hydrator, to decide that getters will be extracted. It
checks, if the key that should be extracted starts with `get` or looks like this
`Zend\Foo\Bar::getFoo`

- **Zend\\Stdlib\\Hydrator\\Filter\\HasFilter**

    This filter is used in the `ClassMethods` hydrator, to decide that `has` methods will be
extracted. It checks, if the key that should be extracted starts with `has` or looks like this
`Zend\Foo\Bar::hasFoo`

- **Zend\\Stdlib\\Hydrator\\Filter\\IsFilter**

    This filter is used in the `ClassMethods` hydrator, to decide that `is` methods will be
extracted. It checks, if the key that should be extracted starts with `is` or looks like this
`Zend\Foo\Bar::isFoo`

- **Zend\\Stdlib\\Hydrator\\Filter\\MethodMatchFilter**

    This filter allows you to strip methods from the extraction with the correct condition in the
composite. It checks, if the key that should be extracted matches a method name. Either
`getServiceLocator` or `Zend\Foo::getServicelocator`. The name of the method is specified in the
constructor of this filter. The 2nd parameter decides whether to use white or blacklisting to
decide. Default is blacklisting - pass `false` to change it.

- **Zend\\Stdlib\\Hydrator\\Filter\\NumberOfParameterFilter**

    This filter is used in the `ClassMethods` hydrator, to check the number of parameters. By
convention, the `get`, `has` and `is` methods do not get any parameters - but it may happen. You can
add your own number of needed parameters, simply add the number to the constructor. The default
value is 0

## Remove filters

If you want to tell e.g. the `ClassMethods` hydrator, to not extract methods that start with `is`,
you can do so:

```php
$hydrator = new ClassMethods(false);
$hydrator->removeFilter("is");
```

The key / value pairs for `is` methods will not end up in your extracted array anymore. The filters
can be used in any hydrator, but the `ClassMethods` hydrator is the only one, that has
pre-registered filters:

```php
$this->filterComposite->addFilter("is", new IsFilter());
$this->filterComposite->addFilter("has", new HasFilter());
$this->filterComposite->addFilter("get", new GetFilter());
$this->filterComposite->addFilter("parameter", new NumberOfParameterFilter(),
FilterComposite::CONDITION_AND);
```

If you're not fine with this, you can unregister them as above.

> ## Note
The parameter for the filter on the `ClassMethods` looks like this by default
`Zend\Foo\Bar::methodName`

## Add filters

You can easily add filters to any hydrator, that extends the `AbstractHydrator`. You can use the
`FilterInterface` or any callable:

```php
$hydrator->addFilter("len", function($property) {
    if (strlen($property) !== 3) {
        return false;
    }
    return true;
});
```

By default, every filter you add will be added with a conditional `or`. If you want to add it with
`and` (as the `NumberOfParameterFilter` that is added to the `ClassMethods` hydrator by default) you
can do that too:

```php
$hydrator->addFilter("len", function($property) {
    if (strlen($property) !== 3) {
        return false;
    }
    return true;
}, FilterComposite::CONDITION_AND);
```

Or you can add the shipped ones:

```php
$hydrator->addFilter(
  "servicemanager",
  new MethodMatchFilter("getServiceManager"),
  FilterComposite::CONDITION_AND
);
```

The example above will exclude the `getServiceManager` method or the key from the extraction, even
if the `get` filter wants to add it.

## Use the composite for complex filters

The composite implements the `FilterInterface` too, so you can add it as a regular filter to the
hydrator. One goody of this implementation, is that you can add the filters with a condition and you
can do even more complex stuff with different composites with different conditions. You can pass the
condition to the 3rd parameter, when you add a filter:

**Zend\\Stdlib\\Hydrator\\Filter\\FilterComposite::CONDITION\_OR**

> At one level of the composite, one of all filters in that condition block has to return true in
order to get extracted

**Zend\\Stdlib\\Hydrator\\Filter\\FilterComposite::CONDITION\_AND**

> At one level of the composite, all of the filters in that condition block has to return `true` in
order to get extracted

This composition will have a similar logic as the if below:

```php
$composite = new FilterComposite();

$composite->addFilter("one", $condition1);
$composite->addFilter("two", $condition2);
$composite->addFilter("three", $condition3);
$composite->addFilter("four", $condition4, FilterComposite::CONDITION_AND);
$composite->addFilter("five", $condition5, FilterComposite::CONDITION_AND);

// This is what's happening internally
if (
     (
        $condition1
        || $condition2
        || $condition3
     ) && (
        $condition4
        && $condition5
     )
 ) {
//do extraction
}
```

If you've only one condition (only `and` or `or`) block, the other one will be completely ignored.

A bit more complex filter can look like this:

```php
$composite = new FilterComposite();
$composite->addFilter(
    "servicemanager",
    new MethodMatchFilter("getServiceManager"),
    FilterComposite::CONDITION_AND
);
$composite->addFilter(
    "eventmanager",
    new MethodMatchFilter("getEventManager"),
    FilterComposite::CONDITION_AND
);

$hydrator->addFilter("excludes", $composite, FilterComposite::CONDITION_AND);

// Internal
if (
     (  // default composite inside the hydrator
        (
            $getFilter
            || $hasFilter
            || $isFilter
         ) && (
            $numberOfParameterFilter
         )
     ) && (  // new composite, added to the one above
        $serviceManagerFilter
        && $eventManagerFilter
     )
) {
// do extraction
}
```

If you perform this on the `ClassMethods` hydrator, all getters will get extracted, but not
`getServiceManager` and `getEventManager`.

## Using the provider interface

There is also a provider interface, that allows you to configure the behavior of the hydrator inside
your objects.

```php
namespace Zend\Stdlib\Hydrator\Filter;

interface FilterProviderInterface
{
    /**
     * Provides a filter for hydration
     *
     * @return FilterInterface
     */
    public function getFilter();
}
```

The `getFilter()` method is getting automatically excluded from `extract()`. If the extracted object
implements the `Zend\Stdlib\Hydrator\Filter\FilterProviderInterface`, the returned `FilterInterface`
instance can also be a `FilterComposite`.

For example:

```php
Class Foo implements FilterProviderInterface
{
     public function getFoo()
     {
         return "foo";
     }

     public function hasFoo()
     {
         return true;
     }

     public function getServiceManager()
     {
         return "servicemanager";
     }

     public function getEventManager()
     {
         return "eventmanager";
     }

     public function getFilter()
     {
         $composite = new FilterComposite();
         $composite->addFilter("get", new GetFilter());

         $exclusionComposite = new FilterComposite();
         $exclusionComposite->addFilter(
             "servicemanager",
             new MethodMatchFilter("getServiceManager"),
             FilterComposite::CONDITION_AND
             );
         $exclusionComposite->addFilter(
             "eventmanager",
             new MethodMatchFilter("getEventManager"),
             FilterComposite::CONDITION_AND
         );

         $composite->addFilter("excludes", $exclusionComposite, FilterComposite::CONDITION_AND);

         return $composite;
     }
}

$hydrator = new ClassMethods(false);
$extractedArray = $hydrator->extract(new Foo());
```

The `$extractedArray` does only have "foo" =&gt; "foo" in. All of the others are excluded from the
extraction.

> ## Note
All pre-registered filters from the `ClassMethods` hydrator are ignored if this interface is used.
