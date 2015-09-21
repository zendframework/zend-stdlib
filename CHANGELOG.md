# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.7.0 - TBD

### Added

- [#19](https://github.com/zendframework/zend-stdlib/pull/19) adds a new
  `FastPriorityQueue` implementation. It follows the same signature as
  `SplPriorityQueue`, but uses a performance-optimized algorithm:

  - inserts are 2x faster than `SplPriorityQueue` and 3x faster than the
    `Zend\Stdlib\PriorityQueue` implementation.
  - extracts are 4x faster than `SplPriorityQueue` and 4-5x faster than the
    `Zend\Stdlib\PriorityQueue` implementation.

  The intention is to use this as a drop-in replacement in the
  `zend-eventmanager` component to provide performance benefits.

### Deprecated

- [#20](https://github.com/zendframework/zend-stdlib/pull/20) deprecates *all
  hydrator* classes, in favor of the new [zend-hydrator](https://github.com/zendframework/zend-hydrator)
  component. All classes were updated to extend their zend-hydrator equivalents,
  and marked as `@deprecated`, indicating the equivalent class from the other
  repository.

  Users *should* immediately start changing their code to use the zend-hydrator
  equivalents; in most cases, this can be as easy as removing the `Stdlib`
  namespace from import statements or hydrator configuration. Hydrators will be
  removed entirely from zend-stdlib in v3.0, and all future updates to hydrators
  will occur in the zend-hydrator library.

  Changes with backwards compatibility implications:

  - Users implementing `Zend\Stdlib\Hydrator\HydratorAwareInterface` will need to
    update their `setHydrator()` implementation to typehint on
    `Zend\Hydrator\HydratorInterface`. This can be done by changing the import
    statement for that interface as follows:

    ```php
    // Replace this:
    use Zend\Stdlib\Hydrator\HydratorInterface;
    // with this:
    use Zend\Hydrator\HydratorInterface;
    ```

    If you are not using imports, change the typehint within the signature itself:

    ```php
    // Replace this:
    public function setHydrator(\Zend\Stdlib\Hydrator\HydratorInterface $hydrator)
    // with this:
    public function setHydrator(\Zend\Hydrator\HydratorInterface $hydrator)
    ```

    If you are using `Zend\Stdlib\Hydrator\HydratorAwareTrait`, no changes are
    necessary, unless you override that method.

  - If you were catching hydrator-generated exceptions, these were previously in
    the `Zend\Stdlib\Exception` namespace. You will need to update your code to
    catch exceptions in the `Zend\Hydrator\Exception` namespace.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.6.1 - TBD

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.6.0 - 2015-07-21

### Added

- [#13](https://github.com/zendframework/zend-stdlib/pull/13) adds
  `Zend\Stdlib\Hydrator\Iterator`, which provides mechanisms for hydrating
  objects when iterating a traversable. This allows creating generic collection
  resultsets; the original idea was pulled from
  [PhlyMongo](https://github.com/phly/PhlyMongo), where it was used to hydrate
  collections retrieved from MongoDB.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.5.2 - 2015-07-21

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#9](https://github.com/zendframework/zend-stdlib/pull/9) fixes an issue with
  count incrementation during insert in PriorityList, ensuring that incrementation only
  occurs when the item inserted was not previously present in the list.

## 2.4.4 - 2015-07-21

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#9](https://github.com/zendframework/zend-stdlib/pull/9) fixes an issue with
  count incrementation during insert in PriorityList, ensuring that incrementation only
  occurs when the item inserted was not previously present in the list.
