<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace ZendTest\Stdlib\TestAsset;

class Reflection
{
    public $foo = '1';

    protected $fooBar = '2';

    private $fooBarBaz = '3';

    public function getFooBar()
    {
        return $this->fooBar;
    }

    public function getFooBarBaz()
    {
        return $this->fooBarBaz;
    }
}
