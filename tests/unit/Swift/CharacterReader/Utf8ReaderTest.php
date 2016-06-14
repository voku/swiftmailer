<?php

class Swift_CharacterReader_Utf8ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Swift_CharacterReader_Utf8Reader
     */
    private $_reader;

    public function setUp()
    {
        $this->_reader = new Swift_CharacterReader_Utf8Reader();
    }

    public function testLeading7BitOctetCausesReturnZero()
    {
        for ($ordinal = 0x00; $ordinal <= 0x7F; ++$ordinal) {
            self::assertSame(
                0,
                $this->_reader->validateByteSequence(array($ordinal), 1)
            );
        }
    }

    public function testLeadingByteOf2OctetCharCausesReturn1()
    {
        for ($octet = 0xC0; $octet <= 0xDF; ++$octet) {
            self::assertSame(
                1,
                $this->_reader->validateByteSequence(array($octet), 1)
            );
        }
    }

    public function testLeadingByteOf3OctetCharCausesReturn2()
    {
        for ($octet = 0xE0; $octet <= 0xEF; ++$octet) {
            self::assertSame(
                2,
                $this->_reader->validateByteSequence(array($octet), 1)
            );
        }
    }

    public function testLeadingByteOf4OctetCharCausesReturn3()
    {
        for ($octet = 0xF0; $octet <= 0xF7; ++$octet) {
            self::assertSame(
                3,
                $this->_reader->validateByteSequence(array($octet), 1)
            );
        }
    }

    public function testLeadingByteOf5OctetCharCausesReturn4()
    {
        for ($octet = 0xF8; $octet <= 0xFB; ++$octet) {
            self::assertSame(
                4,
                $this->_reader->validateByteSequence(array($octet), 1)
            );
        }
    }

    public function testLeadingByteOf6OctetCharCausesReturn5()
    {
        for ($octet = 0xFC; $octet <= 0xFD; ++$octet) {
            self::assertSame(
                5,
                $this->_reader->validateByteSequence(array($octet), 1)
            );
        }
    }
}
