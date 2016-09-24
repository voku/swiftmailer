<?php

/**
 * Class Swift_Mime_AttachmentAcceptanceTest
 */
class Swift_Mime_AttachmentAcceptanceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Swift_Mime_ContentEncoder_Base64ContentEncoder
     */
    private $_contentEncoder;

    /**
     * @var Swift_KeyCache_ArrayKeyCache
     */
    private $_cache;

    /**
     * @var Swift_EmailValidatorBridge
     */
    private $_emailValidator;

    /**
     * @var Swift_Mime_IdGenerator
     */
    private $_idGenerator;

    /**
     * @var Swift_Mime_SimpleHeaderSet
     */
    private $_headers;

    public function setUp()
    {
        $this->_cache = new Swift_KeyCache_ArrayKeyCache(
            new Swift_KeyCache_SimpleKeyCacheInputStream()
        );
        $factory = new Swift_CharacterReaderFactory_SimpleCharacterReaderFactory();
        $this->_contentEncoder = new Swift_Mime_ContentEncoder_Base64ContentEncoder();

        $headerEncoder = new Swift_Mime_HeaderEncoder_QpHeaderEncoder(
            new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8')
        );
        $paramEncoder = new Swift_Encoder_Rfc2231Encoder(
            new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8')
        );
        $this->_emailValidator = new Swift_EmailValidatorBridge();
        $this->_idGenerator = new Swift_Mime_IdGenerator('example.com');
        $this->_headers = new Swift_Mime_SimpleHeaderSet(
            new Swift_Mime_SimpleHeaderFactory($headerEncoder, $paramEncoder, $this->_emailValidator)
        );
    }

    public function testDispositionIsSetInHeader()
    {
        $attachment = $this->_createAttachment();
        $attachment->setContentType('application/pdf');
        $attachment->setDisposition('inline');
        $this->assertSame(
            'Content-Type: application/pdf'."\r\n".
            'Content-Transfer-Encoding: base64'."\r\n".
            'Content-Disposition: inline'."\r\n",
            $attachment->toString()
        );
    }

    public function testDispositionIsAttachmentByDefault()
    {
        $attachment = $this->_createAttachment();
        $attachment->setContentType('application/pdf');
        $this->assertSame(
            'Content-Type: application/pdf'."\r\n".
            'Content-Transfer-Encoding: base64'."\r\n".
            'Content-Disposition: attachment'."\r\n",
            $attachment->toString()
        );
    }

    public function testFilenameIsSetInHeader()
    {
        $attachment = $this->_createAttachment();
        $attachment->setContentType('application/pdf');
        $attachment->setFilename('foo.pdf');
        $this->assertSame(
            'Content-Type: application/pdf; name=foo.pdf'."\r\n".
            'Content-Transfer-Encoding: base64'."\r\n".
            'Content-Disposition: attachment; filename=foo.pdf'."\r\n",
            $attachment->toString()
        );
    }

    public function testSizeIsSetInHeader()
    {
        $attachment = $this->_createAttachment();
        $attachment->setContentType('application/pdf');
        $attachment->setSize(12340);
        $this->assertSame(
            'Content-Type: application/pdf'."\r\n".
            'Content-Transfer-Encoding: base64'."\r\n".
            'Content-Disposition: attachment; size=12340'."\r\n",
            $attachment->toString()
        );
    }

    public function testMultipleParametersInHeader()
    {
        $attachment = $this->_createAttachment();
        $attachment->setContentType('application/pdf');
        $attachment->setFilename('foo.pdf');
        $attachment->setSize(12340);
        $this->assertSame(
            'Content-Type: application/pdf; name=foo.pdf'."\r\n".
            'Content-Transfer-Encoding: base64'."\r\n".
            'Content-Disposition: attachment; filename=foo.pdf; size=12340'."\r\n",
            $attachment->toString()
        );
    }

    public function testEndToEnd()
    {
        $attachment = $this->_createAttachment();
        $attachment->setContentType('application/pdf');
        $attachment->setFilename('foo.pdf');
        $attachment->setSize(12340);
        $attachment->setBody('abcd');
        $this->assertSame(
            'Content-Type: application/pdf; name=foo.pdf'."\r\n".
            'Content-Transfer-Encoding: base64'."\r\n".
            'Content-Disposition: attachment; filename=foo.pdf; size=12340'."\r\n".
            "\r\n".
            base64_encode('abcd'),
            $attachment->toString()
        );
    }

    // -- Private helpers

    /**
     * @return Swift_Mime_Attachment
     */
    protected function _createAttachment()
    {
        $entity = new Swift_Mime_Attachment(
            $this->_headers,
            $this->_contentEncoder,
            $this->_cache,
            $this->_idGenerator
        );

        return $entity;
    }
}
