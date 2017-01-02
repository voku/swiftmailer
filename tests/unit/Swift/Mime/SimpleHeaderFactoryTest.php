<?php

class Swift_Mime_SimpleHeaderFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Swift_Mime_SimpleHeaderFactory
     */
    private $_factory;

    public function setUp()
    {
        $this->_factory = $this->_createFactory();
    }

    public function testMailboxHeaderIsCorrectType()
    {
        $header = $this->_factory->createMailboxHeader('X-Foo');
        $this->assertInstanceOf('Swift_Mime_Headers_MailboxHeader', $header);
    }

    public function testMailboxHeaderHasCorrectName()
    {
        $header = $this->_factory->createMailboxHeader('X-Foo');
        $this->assertSame('X-Foo', $header->getFieldName());
    }

    public function testMailboxHeaderHasCorrectModel()
    {
        $header = $this->_factory->createMailboxHeader(
            'X-Foo',
            array('foo@bar.com' => 'FooBar')
        );
        $this->assertSame(array('foo@bar.com' => 'FooBar'), $header->getFieldBodyModel());
    }

    public function testDateHeaderHasCorrectType()
    {
        $header = $this->_factory->createDateHeader('X-Date');
        $this->assertInstanceOf('Swift_Mime_Headers_DateHeader', $header);
    }

    public function testDateHeaderHasCorrectName()
    {
        $header = $this->_factory->createDateHeader('X-Date');
        $this->assertSame('X-Date', $header->getFieldName());
    }

    public function testDateHeaderHasCorrectModel()
    {
        $header = $this->_factory->createDateHeader('X-Date', 123);
        $this->assertSame(123, $header->getFieldBodyModel());
    }

    public function testTextHeaderHasCorrectType()
    {
        $header = $this->_factory->createTextHeader('X-Foo');
        $this->assertInstanceOf('Swift_Mime_Headers_UnstructuredHeader', $header);
    }

    public function testTextHeaderHasCorrectName()
    {
        $header = $this->_factory->createTextHeader('X-Foo');
        $this->assertSame('X-Foo', $header->getFieldName());
    }

    public function testTextHeaderHasCorrectModel()
    {
        $header = $this->_factory->createTextHeader('X-Foo', 'bar');
        $this->assertSame('bar', $header->getFieldBodyModel());
    }

    public function testParameterizedHeaderHasCorrectType()
    {
        $header = $this->_factory->createParameterizedHeader('X-Foo');
        $this->assertInstanceOf('Swift_Mime_Headers_ParameterizedHeader', $header);
    }

    public function testParameterizedHeaderHasCorrectName()
    {
        $header = $this->_factory->createParameterizedHeader('X-Foo');
        $this->assertSame('X-Foo', $header->getFieldName());
    }

    public function testParameterizedHeaderHasCorrectModel()
    {
        $header = $this->_factory->createParameterizedHeader('X-Foo', 'bar');
        $this->assertSame('bar', $header->getFieldBodyModel());
    }

    public function testParameterizedHeaderHasCorrectParams()
    {
        $header = $this->_factory->createParameterizedHeader(
            'X-Foo', 'bar',
            array('zip' => 'button')
        );
        $this->assertSame(array('zip' => 'button'), $header->getParameters());
    }

    public function testIdHeaderHasCorrectType()
    {
        $header = $this->_factory->createIdHeader('X-ID');
        $this->assertInstanceOf('Swift_Mime_Headers_IdentificationHeader', $header);
    }

    public function testIdHeaderHasCorrectName()
    {
        $header = $this->_factory->createIdHeader('X-ID');
        $this->assertSame('X-ID', $header->getFieldName());
    }

    public function testIdHeaderHasCorrectModel()
    {
        $header = $this->_factory->createIdHeader('X-ID', 'xyz@abc');
        $this->assertSame(array('xyz@abc'), $header->getFieldBodyModel());
    }

    public function testPathHeaderHasCorrectType()
    {
        $header = $this->_factory->createPathHeader('X-Path');
        $this->assertInstanceOf('Swift_Mime_Headers_PathHeader', $header);
    }

    public function testPathHeaderHasCorrectName()
    {
        $header = $this->_factory->createPathHeader('X-Path');
        $this->assertSame('X-Path', $header->getFieldName());
    }

    public function testPathHeaderHasCorrectModel()
    {
        $header = $this->_factory->createPathHeader('X-Path', 'foo@bar.com');
        $this->assertSame('foo@bar.com', $header->getFieldBodyModel());
    }

    public function testCharsetChangeNotificationNotifiesEncoders()
    {
        $encoder = $this->_createHeaderEncoder();
        $encoder->expects($this->once())
                ->method('charsetChanged')
                ->with('utf-8');
        $paramEncoder = $this->_createParamEncoder();
        $paramEncoder->expects($this->once())
                     ->method('charsetChanged')
                     ->with('utf-8');

        $factory = $this->_createFactory($encoder, $paramEncoder);

        $factory->charsetChanged('utf-8');
    }

    // -- Creation methods

    /**
     * @param null $encoder
     * @param null $paramEncoder
     *
     * @return Swift_Mime_SimpleHeaderFactory
     */
    private function _createFactory($encoder = null, $paramEncoder = null)
    {
        return new Swift_Mime_SimpleHeaderFactory(
            $encoder ? $encoder : $this->_createHeaderEncoder(),
            $paramEncoder ? $paramEncoder : $this->_createParamEncoder(),
            new Swift_EmailValidatorBridge()
        );
    }

    /**
     * @return Swift_Mime_HeaderEncoder|PHPUnit_Framework_MockObject_MockObject
     */
    private function _createHeaderEncoder()
    {
        return $this->getMockBuilder('Swift_Mime_HeaderEncoder')->getMock();
    }

    /**
     * @return Swift_Encoder|PHPUnit_Framework_MockObject_MockObject
     */
    private function _createParamEncoder()
    {
        return $this->getMockBuilder('Swift_Encoder')->getMock();
    }
}
