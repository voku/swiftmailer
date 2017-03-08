<?php

/**
 * Class Swift_Mime_EmbeddedFileAcceptanceTest
 */
class Swift_Mime_EmbeddedFileAcceptanceTest extends \PHPUnit_Framework_TestCase
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

    protected function setUp()
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

    public function testContentIdIsSetInHeader()
    {
        $file = $this->_createEmbeddedFile();
        $file->setContentType('application/pdf');
        $file->setId('foo@bar');
        $this->assertSame(
            'Content-Type: application/pdf'."\r\n".
            'Content-Transfer-Encoding: base64'."\r\n".
            'Content-ID: <foo@bar>'."\r\n".
            'Content-Disposition: inline'."\r\n",
            $file->toString()
        );
    }

    public function testDispositionIsSetInHeader()
    {
        $file = $this->_createEmbeddedFile();
        $id = $file->getId();
        $file->setContentType('application/pdf');
        $file->setDisposition('attachment');
        $this->assertSame(
            'Content-Type: application/pdf'."\r\n".
            'Content-Transfer-Encoding: base64'."\r\n".
            'Content-ID: <'.$id.'>'."\r\n".
            'Content-Disposition: attachment'."\r\n",
            $file->toString()
        );
    }

    public function testFilenameIsSetInHeader()
    {
        $file = $this->_createEmbeddedFile();
        $id = $file->getId();
        $file->setContentType('application/pdf');
        $file->setFilename('foo.pdf');
        $this->assertSame(
            'Content-Type: application/pdf; name=foo.pdf'."\r\n".
            'Content-Transfer-Encoding: base64'."\r\n".
            'Content-ID: <'.$id.'>'."\r\n".
            'Content-Disposition: inline; filename=foo.pdf'."\r\n",
            $file->toString()
        );
    }

    public function testSizeIsSetInHeader()
    {
        $file = $this->_createEmbeddedFile();
        $id = $file->getId();
        $file->setContentType('application/pdf');
        $file->setSize(12340);
        $this->assertSame(
            'Content-Type: application/pdf'."\r\n".
            'Content-Transfer-Encoding: base64'."\r\n".
            'Content-ID: <'.$id.'>'."\r\n".
            'Content-Disposition: inline; size=12340'."\r\n",
            $file->toString()
        );
    }

    public function testMultipleParametersInHeader()
    {
        $file = $this->_createEmbeddedFile();
        $id = $file->getId();
        $file->setContentType('application/pdf');
        $file->setFilename('foo.pdf');
        $file->setSize(12340);

        $this->assertSame(
            'Content-Type: application/pdf; name=foo.pdf'."\r\n".
            'Content-Transfer-Encoding: base64'."\r\n".
            'Content-ID: <'.$id.'>'."\r\n".
            'Content-Disposition: inline; filename=foo.pdf; size=12340'."\r\n",
            $file->toString()
        );
    }

    public function testEndToEnd()
    {
        $file = $this->_createEmbeddedFile();
        $id = $file->getId();
        $file->setContentType('application/pdf');
        $file->setFilename('foo.pdf');
        $file->setSize(12340);
        $file->setBody('abcd');
        $this->assertSame(
            'Content-Type: application/pdf; name=foo.pdf'."\r\n".
            'Content-Transfer-Encoding: base64'."\r\n".
            'Content-ID: <'.$id.'>'."\r\n".
            'Content-Disposition: inline; filename=foo.pdf; size=12340'."\r\n".
            "\r\n".
            base64_encode('abcd'),
            $file->toString()
        );
    }

    // -- Private helpers

    /**
     * @return Swift_Mime_EmbeddedFile
     */
    protected function _createEmbeddedFile()
    {
        $entity = new Swift_Mime_EmbeddedFile(
            $this->_headers,
            $this->_contentEncoder,
            $this->_cache,
            $this->_idGenerator
        );

        return $entity;
    }
}
