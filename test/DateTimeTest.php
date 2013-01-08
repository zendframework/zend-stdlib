<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace ZendTest\Stdlib;

use Zend\Stdlib\DateTime;

/**
* @category Zend
* @package Zend_Feed
* @subpackage UnitTests
* @group Zend_Feed
* @group Zend_Feed_Reader
*/
class AtomTest extends \PHPUnit_Framework_TestCase
{
    public $dateTime;
    
    public function testCreatesIS08601WithoutFractionalSeconds()
    {
        $time = '2009-03-07T08:03:50Z';
        
        $date = DateTime::createISO8601Date($time);
        
        $this->assertEquals( \DateTime::createFromFormat(\DateTime::ISO8601, $time), $date);
    }
    
    public function testCreatesIS08601WithFractionalSeconds()
    {
        $time = '2009-03-07T08:03:50.012Z';
        
        $date = DateTime::createISO8601Date($time);
        
        $standard = \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', $time);
        
        $this->assertEquals( $standard, $date);
    }
    
}