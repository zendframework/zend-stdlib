<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib;

interface ParameterObjectInterface
{
    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set(string $key, $value): void;

    /**
     * @param string $key
     * @return mixed
     */
    public function __get(string $key);

    /**
     * @param string $key
     * @return bool
     */
    public function __isset(string $key): bool;

    /**
     * @param string $key
     * @return void
     */
    public function __unset(string $key): void;
}
