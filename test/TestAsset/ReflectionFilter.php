<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace ZendTest\Stdlib\TestAsset;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage UnitTests
 * @group      Zend_Stdlib
 */
class ReflectionFilter
{
    protected $foo = null;
    protected $bar = null;
    protected $blubb = null;
    protected $quo = null;

    public function __construct()
    {
        $this->foo = "bar";
        $this->bar = "foo";
        $this->blubb = "baz";
        $this->quo = "blubb";
    }

}
