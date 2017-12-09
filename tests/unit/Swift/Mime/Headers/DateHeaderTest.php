<?php

class Swift_Mime_Headers_DateHeaderTest extends \PHPUnit\Framework\TestCase
{
    /* --
    The following tests refer to RFC 2822, section 3.6.1 and 3.3.
    */

    public function testTypeIsDateHeader()
    {
        $header = $this->_getHeader('Date');
        $this->assertSame(Swift_Mime_Header::TYPE_DATE, $header->getFieldType());
    }

    public function testGetTimestamp()
    {
        $timestamp = time();
        $header = $this->_getHeader('Date');
        $header->setTimestamp($timestamp);
        $this->assertSame($timestamp, $header->getTimestamp());
    }

    public function testTimestampCanBeSetBySetter()
    {
        $timestamp = time();
        $header = $this->_getHeader('Date');
        $header->setTimestamp($timestamp);
        $this->assertSame($timestamp, $header->getTimestamp());
    }

    public function testIntegerTimestampIsConvertedToRfc2822Date()
    {
        $timestamp = time();
        $header = $this->_getHeader('Date');
        $header->setTimestamp($timestamp);
        $this->assertSame(date('r', $timestamp), $header->getFieldBody());
    }

    public function testSetBodyModel()
    {
        $timestamp = time();
        $header = $this->_getHeader('Date');
        $header->setFieldBodyModel($timestamp);
        $this->assertSame(date('r', $timestamp), $header->getFieldBody());
    }

    public function testGetBodyModel()
    {
        $timestamp = time();
        $header = $this->_getHeader('Date');
        $header->setTimestamp($timestamp);
        $this->assertSame($timestamp, $header->getFieldBodyModel());
    }

    public function testToString()
    {
        $timestamp = time();
        $header = $this->_getHeader('Date');
        $header->setTimestamp($timestamp);
        $this->assertSame('Date: '.date('r', $timestamp)."\r\n",
            $header->toString()
            );
    }

    private function _getHeader($name)
    {
        return new Swift_Mime_Headers_DateHeader($name, new Swift_EmailValidatorBridge());
    }
}
