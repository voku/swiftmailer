<?php

require_once 'swift_required.php';

class Swift_EncodingAcceptanceTest extends \PHPUnit_Framework_TestCase
{
    public function testGet7BitEncodingReturns7BitEncoder()
    {
        $encoder = Swift_Encoding::get7BitEncoding();
        $this->assertSame('7bit', $encoder->getName());
    }

    public function testGet8BitEncodingReturns8BitEncoder()
    {
        $encoder = Swift_Encoding::get8BitEncoding();
        $this->assertSame('8bit', $encoder->getName());
    }

    public function testGetQpEncodingReturnsQpEncoder()
    {
        $encoder = Swift_Encoding::getQpEncoding();
        $this->assertSame('quoted-printable', $encoder->getName());
    }

    public function testGetBase64EncodingReturnsBase64Encoder()
    {
        $encoder = Swift_Encoding::getBase64Encoding();
        $this->assertSame('base64', $encoder->getName());
    }
}
