<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib;

use Zend\Stdlib\DateTime;

/**
 * @group      Zend_Stdlib
 */
class DateTimeTest extends \PHPUnit_Framework_TestCase
{
    public $dateTime;

    public function setUp()
    {
        set_error_handler(function ($errno, $errstr) {
            if (!stristr($errstr, 'datetime extension deprecated')) {
                return false;
            }
            return true;
        }, E_USER_DEPRECATED);
    }

    public function testCreatesIS08601WithoutFractionalSeconds()
    {
        $time = '2009-03-07T08:03:50Z';

        $date = DateTime::createFromISO8601($time);

        $this->assertEquals( \DateTime::createFromFormat(\DateTime::ISO8601, $time), $date);
    }

    public function testCreatesIS08601WithFractionalSeconds()
    {
        $time = '2009-03-07T08:03:50.012Z';

        $date = DateTime::createFromISO8601($time);

        $standard = \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', $time);

        $this->assertEquals( $standard, $date);
    }
}
