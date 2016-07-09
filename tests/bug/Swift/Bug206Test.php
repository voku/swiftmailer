<?php


class Swift_Bug206Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Swift_Mime_HeaderFactory
     */
    private $_factory;

    public function setUp()
    {
        $factory = new Swift_CharacterReaderFactory_SimpleCharacterReaderFactory();
        $headerEncoder = new Swift_Mime_HeaderEncoder_QpHeaderEncoder(
            new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8')
        );
        $paramEncoder = new Swift_Encoder_Rfc2231Encoder(
            new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8')
        );
        $emailValidator = new Swift_EmailValidatorBridge();
        $this->_factory = new Swift_Mime_SimpleHeaderFactory($headerEncoder, $paramEncoder, $emailValidator);
    }

    public function testMailboxHeaderEncoding()
    {
        $this->_testHeaderIsFullyEncoded('email@example.org', 'Family Name, Name', 'To: "Family Name, Name" <email@example.org>' . "\n");
        $this->_testHeaderIsFullyEncoded('email@example.org', 'Family Namé, Name', 'To: Family =?utf-8?Q?Nam=C3=A9=2C?= Name <email@example.org>' . "\n");
        $this->_testHeaderIsFullyEncoded('email@example.org', 'Family Namé , Name', 'To: Family =?utf-8?Q?Nam=C3=A9_=2C?= Name <email@example.org>' . "\n");
        $this->_testHeaderIsFullyEncoded('email@example.org', 'Family Namé ;Name', 'To: Family =?utf-8?Q?Nam=C3=A9_=3BName?= <email@example.org>' . "\n");
        $this->_testHeaderIsFullyEncoded('email@example.org', ':Test with unicode ÖÄÜ', 'To: =?utf-8?Q?=3ATest?= with unicode =?utf-8?Q?=C3=96=C3=84=C3=9C?=' . "\n" . ' <email@example.org>' . "\n");
    }

    private function _testHeaderIsFullyEncoded($email, $name, $expected)
    {
        $mailboxHeader = $this->_factory->createMailboxHeader(
            'To',
            array(
                $email => $name,
            )
        );

        $headerBody = $mailboxHeader->toString();

        self::assertEquals(
            str_replace(array("\n", "\r\n", "\r"), "\n", $expected),
            str_replace(array("\n", "\r\n", "\r"), "\n", $headerBody)
        );
    }
}
