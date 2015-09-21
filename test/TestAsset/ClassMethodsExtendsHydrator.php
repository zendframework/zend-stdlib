<?php
/**
 * @see       http://github.com/zendframework/zend-stdlib for the canonical source repository
 * @copyright Copyright (c) 2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-stdlib/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\Stdlib\TestAsset;

use Traversable;
use Zend\Stdlib\Exception;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\Hydrator\AbstractHydrator;
use Zend\Stdlib\Hydrator\HydratorOptionsInterface;
use Zend\Stdlib\Hydrator\Filter\FilterComposite;
use Zend\Stdlib\Hydrator\Filter\FilterProviderInterface;
use Zend\Stdlib\Hydrator\Filter\GetFilter;
use Zend\Stdlib\Hydrator\Filter\HasFilter;
use Zend\Stdlib\Hydrator\Filter\IsFilter;
use Zend\Stdlib\Hydrator\Filter\MethodMatchFilter;
use Zend\Stdlib\Hydrator\Filter\OptionalParametersFilter;
use Zend\Stdlib\Hydrator\NamingStrategy\UnderscoreNamingStrategy;

/**
 * Class Methods Extends
 *
 * Provides the ability to hydrate and extract data from an entity where it
 * might have extended properties.
 *
 * This test asset exists to see how deprecation works; it is associated with
 * the test ZendTest\Stdlib\HydratorDeprecationTest.
 */
class ClassMethodsExtendsHydrator extends AbstractHydrator implements HydratorOptionsInterface
{
    /**
     * Flag defining whether array keys are underscore-separated (true) or camel case (false)
     * @var bool
     */
    protected $underscoreSeparatedKeys = false;

    /**
     * Flag defining whether extended properties can be extracted
     * @var bool
     */
    protected $extractExtendedProperties = false;

    /**
     * @var \Zend\Stdlib\Hydrator\Filter\FilterInterface
     */
    protected $callableMethodFilter;

    /**
     * Define if extract values will use camel case or name with underscore
     * @param bool|array $underscoreSeparatedKeys
     */
    public function __construct($underscoreSeparatedKeys = false, $extractExtendedProperties = false)
    {
        parent::__construct();
        $this->setUnderscoreSeparatedKeys($underscoreSeparatedKeys);
        $this->setExtractExtendedProperties($extractExtendedProperties);

        $this->callableMethodFilter = new OptionalParametersFilter();

        $this->filterComposite->addFilter('is', new IsFilter());
        $this->filterComposite->addFilter('has', new HasFilter());
        $this->filterComposite->addFilter('get', new GetFilter());
        $this->filterComposite->addFilter('parameter', new OptionalParametersFilter(), FilterComposite::CONDITION_AND);
    }

    /**
     * @param  array|Traversable                 $options
     * @return ClassMethodsExtends
     * @throws Exception\InvalidArgumentException
     */
    public function setOptions($options)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(
                'The options parameter must be an array or a Traversable'
            );
        }
        if (isset($options['underscoreSeparatedKeys'])) {
            $this->setUnderscoreSeparatedKeys($options['underscoreSeparatedKeys']);
        }
        if (isset($options['extractExtendedProperties'])) {
            $this->setExtractExtendedProperties($options['extractExtendedProperties']);
        }

        return $this;
    }

    /**
     * @param  bool      $underscoreSeparatedKeys
     * @return ClassMethodsExtends
     */
    public function setUnderscoreSeparatedKeys($underscoreSeparatedKeys)
    {
        $this->underscoreSeparatedKeys = (bool) $underscoreSeparatedKeys;

        if ($this->underscoreSeparatedKeys) {
            $this->setNamingStrategy(new UnderscoreNamingStrategy);
        } elseif ($this->getNamingStrategy() instanceof UnderscoreNamingStrategy) {
            $this->removeNamingStrategy();
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function getUnderscoreSeparatedKeys()
    {
        return $this->underscoreSeparatedKeys;
    }

    /**
     * @param bool $extractExtendedProperties
     * @return ClassMethodsExtends
     */
    public function setExtractExtendedProperties($extractExtendedProperties)
    {
        $this->extractExtendedProperties = (bool) $extractExtendedProperties;

        if ($this->extractExtendedProperties) {
            $this->filterComposite->removeFilter('skipExtended');
        } else {
            $this->filterComposite->addFilter('skipExtended', new MethodMatchFilter('getExtended'), FilterComposite::CONDITION_AND);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function getExtractExtendedProperties()
    {
        return $this->extractExtendedProperties;
    }

    /**
     * Extract values from an object with class methods
     *
     * Extracts the getter/setter of the given $object.
     *
     * @param  object                           $object
     * @return array
     * @throws Exception\BadMethodCallException for a non-object $object
     */
    public function extract($object)
    {
        if (!is_object($object)) {
            throw new Exception\BadMethodCallException(
                sprintf('%s expects the provided $object to be a PHP object)', __METHOD__)
            );
        }

        $filter = null;
        if ($object instanceof FilterProviderInterface) {
            $filter = new FilterComposite(
                array($object->getFilter()),
                array(new MethodMatchFilter('getFilter'))
            );
        } else {
            $filter = $this->filterComposite;
        }

        $attributes = array();
        $methods = get_class_methods($object);

        foreach ($methods as $method) {
            if (!$filter->filter(get_class($object) . '::' . $method)) {
                continue;
            }

            if (!$this->callableMethodFilter->filter(get_class($object) . '::' . $method)) {
                continue;
            }

            $attribute = $method;
            if (preg_match('/^get/', $method)) {
                $attribute = substr($method, 3);
                if (!property_exists($object, $attribute)) {
                    $attribute = lcfirst($attribute);
                }
            }

            $attribute = $this->extractName($attribute, $object);
            $attributes[$attribute] = $this->extractValue($attribute, $object->$method(), $object);
        }

        return $attributes;
    }

    /**
     * Hydrate an object by populating getter/setter methods
     *
     * Hydrates an object by getter/setter methods of the object.
     *
     * @param  array                            $data
     * @param  object                           $object
     * @return object
     * @throws Exception\BadMethodCallException for a non-object $object
     */
    public function hydrate(array $data, $object)
    {
        if (!is_object($object)) {
            throw new Exception\BadMethodCallException(
                sprintf('%s expects the provided $object to be a PHP object)', __METHOD__)
            );
        }

        foreach ($data as $property => $value) {
            $method = 'set' . ucfirst($this->hydrateName($property, $data));
            $value = $this->hydrateValue($property, $value, $data);
            if (is_callable(array($object, $method))) {
                $object->$method($value);
            } else if (is_callable(array($object, 'setExtended'))) {
                $object->setExtended($property, $value);
            }
        }

        return $object;
    }
}
