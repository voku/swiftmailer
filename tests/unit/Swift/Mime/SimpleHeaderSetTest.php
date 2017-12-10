<?php

class Swift_Mime_SimpleHeaderSetTest extends \PHPUnit_Framework_TestCase
{
    public function testAddMailboxHeaderDelegatesToFactory()
    {
        $factory = $this->_createFactory();
        $factory->expects(self::once())
                ->method('createMailboxHeader')
                ->with('From', array('person@domain' => 'Person'))
                ->will(self::returnValue($this->_createHeader('From')));

        $set = $this->_createSet($factory);
        $set->addMailboxHeader('From', array('person@domain' => 'Person'));
    }

    public function testAddDateHeaderDelegatesToFactory()
    {
        $dateTime = new DateTimeImmutable();
        $factory = $this->_createFactory();
        $factory->expects($this->once())
            ->method('createDateHeader')
            ->with('Date', $dateTime)
            ->will($this->returnValue($this->_createHeader('Date')));
        $set = $this->_createSet($factory);
        $set->addDateHeader('Date', $dateTime);
    }

    public function testAddTextHeaderDelegatesToFactory()
    {
        $factory = $this->_createFactory();
        $factory->expects(self::once())
                ->method('createTextHeader')
                ->with('Subject', 'some text')
                ->will(self::returnValue($this->_createHeader('Subject')));

        $set = $this->_createSet($factory);
        $set->addTextHeader('Subject', 'some text');
    }

    public function testAddParameterizedHeaderDelegatesToFactory()
    {
        $factory = $this->_createFactory();
        $factory->expects(self::once())
                ->method('createParameterizedHeader')
                ->with('Content-Type', 'text/plain', array('charset' => 'utf-8'))
                ->will(self::returnValue($this->_createHeader('Content-Type')));

        $set = $this->_createSet($factory);
        $set->addParameterizedHeader(
            'Content-Type', 'text/plain',
            array('charset' => 'utf-8')
        );
    }

    public function testAddIdHeaderDelegatesToFactory()
    {
        $factory = $this->_createFactory();
        $factory->expects(self::once())
                ->method('createIdHeader')
                ->with('Message-ID', 'some@id')
                ->will(self::returnValue($this->_createHeader('Message-ID')));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
    }

    public function testAddPathHeaderDelegatesToFactory()
    {
        $factory = $this->_createFactory();
        $factory->expects(self::once())
                ->method('createPathHeader')
                ->with('Return-Path', 'some@path')
                ->will(self::returnValue($this->_createHeader('Return-Path')));

        $set = $this->_createSet($factory);
        $set->addPathHeader('Return-Path', 'some@path');
    }

    public function testHasReturnsFalseWhenNoHeaders()
    {
        $set = $this->_createSet($this->_createFactory());
        self::assertFalse($set->has('Some-Header'));
    }

    public function testAddedMailboxHeaderIsSeenByHas()
    {
        $factory = $this->_createFactory();
        $factory->expects(self::once())
                ->method('createMailboxHeader')
                ->with('From', array('person@domain' => 'Person'))
                ->will(self::returnValue($this->_createHeader('From')));

        $set = $this->_createSet($factory);
        $set->addMailboxHeader('From', array('person@domain' => 'Person'));
        self::assertTrue($set->has('From'));
    }

    public function testAddedDateHeaderIsSeenByHas()
    {
        $dateTime = new DateTimeImmutable();
        $factory = $this->_createFactory();
        $factory->expects($this->once())
            ->method('createDateHeader')
            ->with('Date', $dateTime)
            ->will($this->returnValue($this->_createHeader('Date')));
        $set = $this->_createSet($factory);
        $set->addDateHeader('Date', $dateTime);
        $this->assertTrue($set->has('Date'));
    }

    public function testAddedTextHeaderIsSeenByHas()
    {
        $factory = $this->_createFactory();
        $factory->expects(self::once())
                ->method('createTextHeader')
                ->with('Subject', 'some text')
                ->will(self::returnValue($this->_createHeader('Subject')));

        $set = $this->_createSet($factory);
        $set->addTextHeader('Subject', 'some text');
        self::assertTrue($set->has('Subject'));
    }

    public function testAddedParameterizedHeaderIsSeenByHas()
    {
        $factory = $this->_createFactory();
        $factory->expects(self::once())
                ->method('createParameterizedHeader')
                ->with('Content-Type', 'text/plain', array('charset' => 'utf-8'))
                ->will(self::returnValue($this->_createHeader('Content-Type')));

        $set = $this->_createSet($factory);
        $set->addParameterizedHeader(
            'Content-Type', 'text/plain',
            array('charset' => 'utf-8')
        );
        self::assertTrue($set->has('Content-Type'));
    }

    public function testAddedIdHeaderIsSeenByHas()
    {
        $factory = $this->_createFactory();
        $factory->expects(self::once())
                ->method('createIdHeader')
                ->with('Message-ID', 'some@id')
                ->will(self::returnValue($this->_createHeader('Message-ID')));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        self::assertTrue($set->has('Message-ID'));
    }

    public function testAddedPathHeaderIsSeenByHas()
    {
        $factory = $this->_createFactory();
        $factory->expects(self::once())
                ->method('createPathHeader')
                ->with('Return-Path', 'some@path')
                ->will(self::returnValue($this->_createHeader('Return-Path')));

        $set = $this->_createSet($factory);
        $set->addPathHeader('Return-Path', 'some@path');
        self::assertTrue($set->has('Return-Path'));
    }

    public function testNewlySetHeaderIsSeenByHas()
    {
        $factory = $this->_createFactory();
        $header = $this->_createHeader('X-Foo', 'bar');
        $set = $this->_createSet($factory);
        $set->set($header);
        self::assertTrue($set->has('X-Foo'));
    }

    public function testHasCanAcceptOffset()
    {
        $factory = $this->_createFactory();
        $factory->expects(self::once())
                ->method('createIdHeader')
                ->with('Message-ID', 'some@id')
                ->will(self::returnValue($this->_createHeader('Message-ID')));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        self::assertTrue($set->has('Message-ID', 0));
    }

    public function testHasWithIllegalOffsetReturnsFalse()
    {
        $factory = $this->_createFactory();
        $factory->expects(self::once())
                ->method('createIdHeader')
                ->with('Message-ID', 'some@id')
                ->will(self::returnValue($this->_createHeader('Message-ID')));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        self::assertFalse($set->has('Message-ID', 1));
    }

    public function testHasCanDistinguishMultipleHeaders()
    {
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createIdHeader')
                ->with('Message-ID', 'some@id')
                ->will(self::returnValue($this->_createHeader('Message-ID')));
        $factory->expects(self::at(1))
                ->method('createIdHeader')
                ->with('Message-ID', 'other@id')
                ->will(self::returnValue($this->_createHeader('Message-ID')));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->addIdHeader('Message-ID', 'other@id');
        self::assertTrue($set->has('Message-ID', 1));
    }

    public function testGetWithUnspecifiedOffset()
    {
        $header = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects(self::once())
                ->method('createIdHeader')
                ->with('Message-ID', 'some@id')
                ->will(self::returnValue($header));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        self::assertSame($header, $set->get('Message-ID'));
    }

    public function testGetWithSpeiciedOffset()
    {
        $header0 = $this->_createHeader('Message-ID');
        $header1 = $this->_createHeader('Message-ID');
        $header2 = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createIdHeader')
                ->with('Message-ID', 'some@id')
                ->will(self::returnValue($header0));
        $factory->expects(self::at(1))
                ->method('createIdHeader')
                ->with('Message-ID', 'other@id')
                ->will(self::returnValue($header1));
        $factory->expects(self::at(2))
                ->method('createIdHeader')
                ->with('Message-ID', 'more@id')
                ->will(self::returnValue($header2));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->addIdHeader('Message-ID', 'other@id');
        $set->addIdHeader('Message-ID', 'more@id');
        self::assertSame($header1, $set->get('Message-ID', 1));
    }

    public function testGetReturnsNullIfHeaderNotSet()
    {
        $set = $this->_createSet($this->_createFactory());
        self::assertNull($set->get('Message-ID', 99));
    }

    public function testGetAllReturnsAllHeadersMatchingName()
    {
        $header0 = $this->_createHeader('Message-ID');
        $header1 = $this->_createHeader('Message-ID');
        $header2 = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createIdHeader')
                ->with('Message-ID', 'some@id')
                ->will(self::returnValue($header0));
        $factory->expects(self::at(1))
                ->method('createIdHeader')
                ->with('Message-ID', 'other@id')
                ->will(self::returnValue($header1));
        $factory->expects(self::at(2))
                ->method('createIdHeader')
                ->with('Message-ID', 'more@id')
                ->will(self::returnValue($header2));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->addIdHeader('Message-ID', 'other@id');
        $set->addIdHeader('Message-ID', 'more@id');

        self::assertSame(
            array($header0, $header1, $header2,),
            $set->getAll('Message-ID')
        );
    }

    public function testGetAllReturnsAllHeadersIfNoArguments()
    {
        $header0 = $this->_createHeader('Message-ID');
        $header1 = $this->_createHeader('Subject');
        $header2 = $this->_createHeader('To');
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createIdHeader')
                ->with('Message-ID', 'some@id')
                ->will(self::returnValue($header0));
        $factory->expects(self::at(1))
                ->method('createIdHeader')
                ->with('Subject', 'thing')
                ->will(self::returnValue($header1));
        $factory->expects(self::at(2))
                ->method('createIdHeader')
                ->with('To', 'person@example.org')
                ->will(self::returnValue($header2));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->addIdHeader('Subject', 'thing');
        $set->addIdHeader('To', 'person@example.org');

        self::assertSame(
            array($header0, $header1, $header2,),
            $set->getAll()
        );
    }

    public function testGetAllReturnsEmptyArrayIfNoneSet()
    {
        $set = $this->_createSet($this->_createFactory());
        self::assertSame(array(), $set->getAll('Received'));
    }

    public function testRemoveWithUnspecifiedOffset()
    {
        $header = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createIdHeader')
                ->with('Message-ID', 'some@id')
                ->will(self::returnValue($header));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->remove('Message-ID');
        self::assertFalse($set->has('Message-ID'));
    }

    public function testRemoveWithSpecifiedIndexRemovesHeader()
    {
        $header0 = $this->_createHeader('Message-ID');
        $header1 = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createIdHeader')
                ->with('Message-ID', 'some@id')
                ->will(self::returnValue($header0));
        $factory->expects(self::at(1))
                ->method('createIdHeader')
                ->with('Message-ID', 'other@id')
                ->will(self::returnValue($header1));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->addIdHeader('Message-ID', 'other@id');
        $set->remove('Message-ID', 0);
        self::assertFalse($set->has('Message-ID', 0));
        self::assertTrue($set->has('Message-ID', 1));
        self::assertTrue($set->has('Message-ID'));
        $set->remove('Message-ID', 1);
        self::assertFalse($set->has('Message-ID', 1));
        self::assertFalse($set->has('Message-ID'));
        self::assertFalse($set->has('Message-ID', 0));
        $set->remove('Message-ID', 0);
        self::assertFalse($set->has('Message-ID', 0));
    }

    public function testRemoveWithSpecifiedIndexLeavesOtherHeaders()
    {
        $header0 = $this->_createHeader('Message-ID');
        $header1 = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createIdHeader')
                ->with('Message-ID', 'some@id')
                ->will(self::returnValue($header0));
        $factory->expects(self::at(1))
                ->method('createIdHeader')
                ->with('Message-ID', 'other@id')
                ->will(self::returnValue($header1));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->addIdHeader('Message-ID', 'other@id');
        $set->remove('Message-ID', 1);
        self::assertTrue($set->has('Message-ID', 0));
    }

    public function testRemoveWithInvalidOffsetDoesNothing()
    {
        $header = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createIdHeader')
                ->with('Message-ID', 'some@id')
                ->will(self::returnValue($header));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->remove('Message-ID', 50);
        self::assertTrue($set->has('Message-ID'));
    }

    public function testRemoveAllRemovesAllHeadersWithName()
    {
        $header0 = $this->_createHeader('Message-ID');
        $header1 = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createIdHeader')
                ->with('Message-ID', 'some@id')
                ->will(self::returnValue($header0));
        $factory->expects(self::at(1))
                ->method('createIdHeader')
                ->with('Message-ID', 'other@id')
                ->will(self::returnValue($header1));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->addIdHeader('Message-ID', 'other@id');
        $set->removeAll('Message-ID');
        self::assertFalse($set->has('Message-ID', 0));
        self::assertFalse($set->has('Message-ID', 1));
    }

    public function testHasIsNotCaseSensitive()
    {
        $header = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createIdHeader')
                ->with('Message-ID', 'some@id')
                ->will(self::returnValue($header));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        self::assertTrue($set->has('message-id'));
    }

    public function testGetIsNotCaseSensitive()
    {
        $header = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createIdHeader')
                ->with('Message-ID', 'some@id')
                ->will(self::returnValue($header));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        self::assertSame($header, $set->get('message-id'));
    }

    public function testGetAllIsNotCaseSensitive()
    {
        $header = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createIdHeader')
                ->with('Message-ID', 'some@id')
                ->will(self::returnValue($header));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        self::assertSame(array($header), $set->getAll('message-id'));
    }

    public function testRemoveIsNotCaseSensitive()
    {
        $header = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createIdHeader')
                ->with('Message-ID', 'some@id')
                ->will(self::returnValue($header));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->remove('message-id');
        self::assertFalse($set->has('Message-ID'));
    }

    public function testRemoveAllIsNotCaseSensitive()
    {
        $header = $this->_createHeader('Message-ID');
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createIdHeader')
                ->with('Message-ID', 'some@id')
                ->will(self::returnValue($header));

        $set = $this->_createSet($factory);
        $set->addIdHeader('Message-ID', 'some@id');
        $set->removeAll('message-id');
        self::assertFalse($set->has('Message-ID'));
    }

    public function testNewInstance()
    {
        $set = $this->_createSet($this->_createFactory());
        $instance = $set->newInstance();
        self::assertInstanceOf('Swift_Mime_HeaderSet', $instance);
    }

    public function testToStringJoinsHeadersTogether()
    {
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createTextHeader')
                ->with('Foo', 'bar')
                ->will(self::returnValue($this->_createHeader('Foo', 'bar')));
        $factory->expects(self::at(1))
                ->method('createTextHeader')
                ->with('Zip', 'buttons')
                ->will(self::returnValue($this->_createHeader('Zip', 'buttons')));

        $set = $this->_createSet($factory);
        $set->addTextHeader('Foo', 'bar');
        $set->addTextHeader('Zip', 'buttons');
        self::assertSame(
            "Foo: bar\r\n" .
            "Zip: buttons\r\n",
            $set->toString()
        );
    }

    public function testHeadersWithoutBodiesAreNotDisplayed()
    {
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createTextHeader')
                ->with('Foo', 'bar')
                ->will(self::returnValue($this->_createHeader('Foo', 'bar')));
        $factory->expects(self::at(1))
                ->method('createTextHeader')
                ->with('Zip', '')
                ->will(self::returnValue($this->_createHeader('Zip', '')));

        $set = $this->_createSet($factory);
        $set->addTextHeader('Foo', 'bar');
        $set->addTextHeader('Zip', '');
        self::assertSame(
            "Foo: bar\r\n",
            $set->toString()
        );
    }

    public function testHeadersWithoutBodiesCanBeForcedToDisplay()
    {
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createTextHeader')
                ->with('Foo', '')
                ->will(self::returnValue($this->_createHeader('Foo', '')));
        $factory->expects(self::at(1))
                ->method('createTextHeader')
                ->with('Zip', '')
                ->will(self::returnValue($this->_createHeader('Zip', '')));

        $set = $this->_createSet($factory);
        $set->addTextHeader('Foo', '');
        $set->addTextHeader('Zip', '');
        $set->setAlwaysDisplayed(array('Foo', 'Zip'));
        self::assertSame(
            "Foo: \r\n" .
            "Zip: \r\n",
            $set->toString()
        );
    }

    public function testHeaderSequencesCanBeSpecified()
    {
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createTextHeader')
                ->with('Third', 'three')
                ->will(self::returnValue($this->_createHeader('Third', 'three')));
        $factory->expects(self::at(1))
                ->method('createTextHeader')
                ->with('First', 'one')
                ->will(self::returnValue($this->_createHeader('First', 'one')));
        $factory->expects(self::at(2))
                ->method('createTextHeader')
                ->with('Second', 'two')
                ->will(self::returnValue($this->_createHeader('Second', 'two')));

        $set = $this->_createSet($factory);
        $set->addTextHeader('Third', 'three');
        $set->addTextHeader('First', 'one');
        $set->addTextHeader('Second', 'two');

        $set->defineOrdering(array('First', 'Second', 'Third'));

        self::assertSame(
            "First: one\r\n" .
            "Second: two\r\n" .
            "Third: three\r\n",
            $set->toString()
        );
    }

    public function testUnsortedHeadersAppearAtEnd()
    {
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createTextHeader')
                ->with('Fourth', 'four')
                ->will(self::returnValue($this->_createHeader('Fourth', 'four')));
        $factory->expects(self::at(1))
                ->method('createTextHeader')
                ->with('Fifth', 'five')
                ->will(self::returnValue($this->_createHeader('Fifth', 'five')));
        $factory->expects(self::at(2))
                ->method('createTextHeader')
                ->with('Third', 'three')
                ->will(self::returnValue($this->_createHeader('Third', 'three')));
        $factory->expects(self::at(3))
                ->method('createTextHeader')
                ->with('First', 'one')
                ->will(self::returnValue($this->_createHeader('First', 'one')));
        $factory->expects(self::at(4))
                ->method('createTextHeader')
                ->with('Second', 'two')
                ->will(self::returnValue($this->_createHeader('Second', 'two')));

        $set = $this->_createSet($factory);
        $set->addTextHeader('Fourth', 'four');
        $set->addTextHeader('Fifth', 'five');
        $set->addTextHeader('Third', 'three');
        $set->addTextHeader('First', 'one');
        $set->addTextHeader('Second', 'two');

        $set->defineOrdering(array('First', 'Second', 'Third'));

        self::assertSame(
            "First: one\r\n" .
            "Second: two\r\n" .
            "Third: three\r\n" .
            "Fourth: four\r\n" .
            "Fifth: five\r\n",
            $set->toString()
        );
    }

    public function testSettingCharsetNotifiesAlreadyExistingHeaders()
    {
        $subject = $this->_createHeader('Subject', 'some text');
        $xHeader = $this->_createHeader('X-Header', 'some text');
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createTextHeader')
                ->with('Subject', 'some text')
                ->will(self::returnValue($subject));
        $factory->expects(self::at(1))
                ->method('createTextHeader')
                ->with('X-Header', 'some text')
                ->will(self::returnValue($xHeader));
        $subject->expects(self::once())
                ->method('setCharset')
                ->with('utf-8');
        $xHeader->expects(self::once())
                ->method('setCharset')
                ->with('utf-8');

        $set = $this->_createSet($factory);
        $set->addTextHeader('Subject', 'some text');
        $set->addTextHeader('X-Header', 'some text');

        $set->setCharset('utf-8');
    }

    public function testCharsetChangeNotifiesAlreadyExistingHeaders()
    {
        $subject = $this->_createHeader('Subject', 'some text');
        $xHeader = $this->_createHeader('X-Header', 'some text');
        $factory = $this->_createFactory();
        $factory->expects(self::at(0))
                ->method('createTextHeader')
                ->with('Subject', 'some text')
                ->will(self::returnValue($subject));
        $factory->expects(self::at(1))
                ->method('createTextHeader')
                ->with('X-Header', 'some text')
                ->will(self::returnValue($xHeader));
        $subject->expects(self::once())
                ->method('setCharset')
                ->with('utf-8');
        $xHeader->expects(self::once())
                ->method('setCharset')
                ->with('utf-8');

        $set = $this->_createSet($factory);
        $set->addTextHeader('Subject', 'some text');
        $set->addTextHeader('X-Header', 'some text');

        $set->charsetChanged('utf-8');
    }

    public function testCharsetChangeNotifiesFactory()
    {
        $factory = $this->_createFactory();
        $factory->expects(self::once())
                ->method('charsetChanged')
                ->with('utf-8');

        $set = $this->_createSet($factory);

        $set->setCharset('utf-8');
    }

    private function _createSet($factory)
    {
        return new Swift_Mime_SimpleHeaderSet($factory);
    }

    /**
     * @return Swift_Mime_HeaderFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private function _createFactory()
    {
        return $this->getMockBuilder('Swift_Mime_HeaderFactory')->getMock();
    }

    /**
     * @param string $name
     * @param string $body
     *
     * @return Swift_Mime_Header|PHPUnit_Framework_MockObject_MockObject
     */
    private function _createHeader($name, $body = '')
    {
        $header = $this->getMockBuilder('Swift_Mime_Header')->getMock();
        $header->expects(self::any())
               ->method('getFieldName')
               ->will(self::returnValue($name));
        $header->expects(self::any())
               ->method('toString')
               ->will(self::returnValue(sprintf("%s: %s\r\n", $name, $body)));
        $header->expects(self::any())
               ->method('getFieldBody')
               ->will(self::returnValue($body));

        return $header;
    }
}
