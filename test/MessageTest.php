<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib;

use PHPUnit\Framework\TestCase;
use Zend\Stdlib\Exception\InvalidArgumentException;
use Zend\Stdlib\Message;

class MessageTest extends TestCase
{
    public function testMessageCanSetAndGetContent()
    {
        $message = new Message();
        $ret = $message->setContent('I can set content');
        $this->assertInstanceOf('Zend\Stdlib\Message', $ret);
        $this->assertEquals('I can set content', $message->getContent());
    }

    public function testMessageCanSetAndGetMetadataKeyAsString()
    {
        $message = new Message();
        $ret = $message->setMetadata('foo', 'bar');
        $this->assertInstanceOf('Zend\Stdlib\Message', $ret);
        $this->assertEquals('bar', $message->getMetadata('foo'));
        $this->assertEquals(['foo' => 'bar'], $message->getMetadata());
    }

    public function testMessageCanSetAndGetMetadataKeyAsArray()
    {
        $message = new Message();
        $ret = $message->setMetadata(['foo' => 'bar']);
        $this->assertInstanceOf('Zend\Stdlib\Message', $ret);
        $this->assertEquals('bar', $message->getMetadata('foo'));
    }

    public function testMessageGetMetadataWillUseDefaultValueIfNoneExist()
    {
        $message = new Message();
        $this->assertEquals('bar', $message->getMetadata('foo', 'bar'));
    }

    public function testMessageThrowsExceptionOnInvalidKeyForMetadataSet()
    {
        $message = new Message();

        $this->expectException(InvalidArgumentException::class);
        $message->setMetadata(new \stdClass());
    }

    public function testMessageThrowsExceptionOnInvalidKeyForMetadataGet()
    {
        $message = new Message();

        $this->expectException(InvalidArgumentException::class);
        $message->getMetadata(new \stdClass());
    }

    public function testMessageToStringWorks()
    {
        $message = new Message();
        $message->setMetadata(['Foo' => 'bar', 'One' => 'Two']);
        $message->setContent('This is my content');
        $expected = "Foo: bar\r\nOne: Two\r\n\r\nThis is my content";
        $this->assertEquals($expected, $message->toString());
    }
}
