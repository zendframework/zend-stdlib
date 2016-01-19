# AggregateHydrator

`Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator` is an implementation of
`Zend\Stdlib\Hydrator\HydratorInterface` that composes multiple hydrators via event listeners.

You typically want to use an aggregate hydrator when you want to hydrate or extract data from
complex objects that implement multiple interfaces, and therefore need multiple hydrators to handle
that in subsequent steps.

## Installation requirements

The `AggregateHydrator` is based on the `Zend\EventManager` component, so be sure to have it
installed before getting started:

```php
php composer.phar require zendframework/zend-eventmanager:2.*
```

## Basic usage

A simple use case may be hydrating a `BlogPost` object, which contains data for the user that
created it, the time it was created, the current publishing status, etc:

```php
use Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator;

$hydrator = new AggregateHydrator();

// attach the various hydrators capable of handling simpler interfaces
$hydrator->add(new My\BlogPostHydrator());
$hydrator->add(new My\UserAwareObjectHydrator());
$hydrator->add(new My\TimestampedObjectHydrator());
$hydrator->add(new My\PublishableObjectHydrator());
// ...

// Now retrieve the BlogPost object
// ...

// you can now extract complex data from a blogpost
$data = $hydrator->extract($blogPost);

// or you can fill the object with complex data
$blogPost = $hydrator->hydrate($data, $blogPost);
```

> ## Note
#### Hydrator priorities
`AggregateHydrator::add` has a second optional argument `$priority`. If you have two or more
hydrators that conflict with each other for same data keys, you may decide which one has to be
executed first or last by passing a higher or lower integer priority to the second argument of
`AggregateHydrator::add`

In order to work with this logic, each of the hydrators that are attached should just ignore any
unknown object type passed in, such as in following example:

```php
namespace My;

use Zend\Stdlib\Hydrator\HydratorInterface

class BlogPostHydrator implements HydratorInterface
{
    public function hydrate($data, $object)
    {
        if (!$object instanceof BlogPost) {
            return $object;
        }

        // ... continue hydration ...
    }

    public function extract($object)
    {
        if (!$object instanceof BlogPost) {
            return array();
        }

        // ... continue extraction ...
    }
}
```

## Advanced use cases

Since the `AggregateHydrator` is event-driven, you can use the `EventManager` API to tweak its
behaviour.

Common use cases are:

> -   Removal of hydrated data keys (passwords/confidential information) depending on business rules
- Caching of the hydration/extraction process
- Transformations on extracted data, for compatibility with third-party APIs

In the following example, a cache listener will be introduced to speed up hydration, which can be
very useful when the same data is requested multiple times:

```php
use Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator;
use Zend\Stdlib\Hydrator\Aggregate\ExtractEvent;
use Zend\Cache\Storage\Adapter\Memory;

$hydrator = new AggregateHydrator();

// attach the various hydrators
$hydrator->add(new My\BlogPostHydrator());
$hydrator->add(new My\UserAwareObjectHydrator());
$hydrator->add(new My\TimestampedObjectHydrator());
$hydrator->add(new My\PublishableObjectHydrator());
// ...

$cache             = new Memory();
$cacheReadListener = function (ExtractEvent $event) use ($cache) {
    $object = $event->getExtractionObject();

    if (!$object instanceof BlogPost) {
        return;
    }

    if ($cache->hasItem($object->getId())) {
        $event->setExtractedData($cache->getItem($object->getId()));
        $event->stopPropagation();
    }
};
$cacheWriteListener = function (ExtractEvent $event) use ($cache) {
    $object = $event->getExtractionObject();

    if (!$object instanceof BlogPost) {
        return;
    }

    $cache->setItem($object->getId(), $event->getExtractedData());
};

// attaching a high priority listener executed before extraction logic
$hydrator->getEventManager()->attach(ExtractEvent::EVENT_EXTRACT, $cacheReadListener, 1000);
// attaching a low priority listener executed after extraction logic
$hydrator->getEventManager()->attach(ExtractEvent::EVENT_EXTRACT, $cacheWriteListener, -1000);
```

With an aggregate hydrator configured in this way, any `$hydrator->extract($blogPost)` operation
will be cached
