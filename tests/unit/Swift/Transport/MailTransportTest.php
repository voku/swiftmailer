<?php

class Swift_Transport_MailTransportTest extends \SwiftMailerTestCase
{
    public function testTransportUsesToFieldBodyInSending()
    {
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($dispatcher);

        $to = $this->_createHeader();
        $headers = $this->_createHeaders(
            array(
                'To' => $to,
            )
        );
        $message = $this->_createMessage($headers);

        $to->shouldReceive('getFieldBody')
           ->zeroOrMoreTimes()
           ->andReturn('Foo <foo@bar>');
        $transport->shouldReceive('mail')
                  ->once()
                  ->with('Foo <foo@bar>', \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    public function testTransportUsesSubjectFieldBodyInSending()
    {
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($dispatcher);

        $subj = $this->_createHeader();
        $headers = $this->_createHeaders(
            array(
                'Subject' => $subj,
            )
        );
        $message = $this->_createMessage($headers);

        $subj->shouldReceive('getFieldBody')
             ->zeroOrMoreTimes()
             ->andReturn('Thing');
        $transport->shouldReceive('mail')
                  ->once()
                  ->with(\Mockery::any(), 'Thing', \Mockery::any(), \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    public function testTransportUsesBodyOfMessage()
    {
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessage($headers);

        $message->shouldReceive('toString')
                ->zeroOrMoreTimes()
                ->andReturn(
                    "To: Foo <foo@bar>\r\n" .
                    "\r\n" .
                    'This body'
                );
        $transport->shouldReceive('mail')
                  ->once()
                  ->with(\Mockery::any(), \Mockery::any(), 'This body', \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    public function testTransportSettingUsingReturnPathForExtraParams()
    {
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessage($headers);
        $message->shouldReceive('getReturnPath')
                ->zeroOrMoreTimes()
                ->andReturn(
                    'foo@bar.de'
                );
        $transport->shouldReceive('mail')
                  ->once()
                  ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any());
        $transport->send($message);
    }

    public function testTransportSettingEmptyExtraParams()
    {
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessage($headers);
        $message->shouldReceive('getReturnPath')
                ->zeroOrMoreTimes()
                ->andReturn(null);
        $message->shouldReceive('getSender')
                ->zeroOrMoreTimes()
                ->andReturn(null);
        $message->shouldReceive('getFrom')
                ->zeroOrMoreTimes()
                ->andReturn(null);
        $transport->shouldReceive('mail')
                  ->once()
                  ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any());
        $transport->send($message);
    }

    public function testTransportSettingSettingExtraParamsWithF()
    {
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($dispatcher);

        $transport->setExtraParams('-x\'foo\' -f%s');
        $headers = $this->_createHeaders();
        $message = $this->_createMessage($headers);
        $message->shouldReceive('getReturnPath')
                ->zeroOrMoreTimes()
                ->andReturn(
                    'foo@bar'
                );
        $message->shouldReceive('getSender')
                ->zeroOrMoreTimes()
                ->andReturn(null);
        $message->shouldReceive('getFrom')
                ->zeroOrMoreTimes()
                ->andReturn(null);
        $transport->shouldReceive('mail')
                  ->once()
                  ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any());
        $transport->send($message);
    }

    public function testTransportSettingSettingExtraParamsWithoutF()
    {
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($dispatcher);
        $transport->setExtraParams('-x\'foo\'');
        $headers = $this->_createHeaders();
        $message = $this->_createMessage($headers);
        $message->shouldReceive('getReturnPath')
                ->zeroOrMoreTimes()
                ->andReturn(
                    'foo@bar'
                );
        $message->shouldReceive('getSender')
                ->zeroOrMoreTimes()
                ->andReturn(null);
        $message->shouldReceive('getFrom')
                ->zeroOrMoreTimes()
                ->andReturn(null);
        $transport->shouldReceive('mail')
                  ->once()
                  ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), '-x\'foo\'');
        $transport->send($message);
    }

    public function testTransportUsesHeadersFromMessage()
    {
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessage($headers);
        $message->shouldReceive('toString')
                ->zeroOrMoreTimes()
                ->andReturn(
                    "Subject: Stuff\r\n" .
                    "\r\n" .
                    'This body'
                );
        $transport->shouldReceive('mail')
                  ->once()
                  ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), 'Subject: Stuff' . PHP_EOL, \Mockery::any());
        $transport->send($message);
    }

    public function testTransportReturnsCountOfAllRecipientsIfReturnsTrue()
    {
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessage($headers);

        $message->shouldReceive('getTo')
                ->zeroOrMoreTimes()
                ->andReturn(array('foo@bar' => null, 'zip@button' => null));
        $message->shouldReceive('getCc')
                ->zeroOrMoreTimes()
                ->andReturn(array('test@test' => null));
        $transport->shouldReceive('mail')
                  ->once()
                  ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any())
                  ->andReturn(true);

        $this->assertEquals(3, $transport->send($message));
    }

    public function testTransportReturnsZeroIfReturnsFalse()
    {
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($dispatcher);

        $headers = $this->_createHeaders();
        $message = $this->_createMessage($headers);

        $message->shouldReceive('getTo')
                ->zeroOrMoreTimes()
                ->andReturn(array('foo@bar' => null, 'zip@button' => null));
        $message->shouldReceive('getCc')
                ->zeroOrMoreTimes()
                ->andReturn(array('test@test' => null));
        $transport->shouldReceive('mail')
                  ->once()
                  ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any())
                  ->andReturn(false);

        $this->assertEquals(0, $transport->send($message));
    }

    public function testToHeaderIsRemovedFromHeaderSetDuringSending()
    {
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($dispatcher);

        $to = $this->_createHeader();
        $headers = $this->_createHeaders(
            array(
                'To' => $to,
            )
        );
        $message = $this->_createMessage($headers);

        $headers->shouldReceive('remove')
                ->once()
                ->with('To');
        $headers->shouldReceive('remove')
                ->zeroOrMoreTimes();
        $transport->shouldReceive('mail')
                  ->once()
                  ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    public function testSubjectHeaderIsRemovedFromHeaderSetDuringSending()
    {
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($dispatcher);

        $subject = $this->_createHeader();
        $headers = $this->_createHeaders(
            array(
                'Subject' => $subject,
            )
        );
        $message = $this->_createMessage($headers);

        $headers->shouldReceive('remove')
                ->once()
                ->with('Subject');
        $headers->shouldReceive('remove')
                ->zeroOrMoreTimes();
        $transport->shouldReceive('mail')
                  ->once()
                  ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    public function testToHeaderIsPutBackAfterSending()
    {
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($dispatcher);

        $to = $this->_createHeader();
        $headers = $this->_createHeaders(
            array(
                'To' => $to,
            )
        );
        $message = $this->_createMessage($headers);

        $headers->shouldReceive('set')
                ->once()
                ->with($to);
        $headers->shouldReceive('set')
                ->zeroOrMoreTimes();
        $transport->shouldReceive('mail')
                  ->once()
                  ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    public function testSubjectHeaderIsPutBackAfterSending()
    {
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($dispatcher);

        $subject = $this->_createHeader();
        $headers = $this->_createHeaders(
            array(
                'Subject' => $subject,
            )
        );
        $message = $this->_createMessage($headers);

        $headers->shouldReceive('set')
                ->once()
                ->with($subject);
        $headers->shouldReceive('set')
                ->zeroOrMoreTimes();
        $transport->shouldReceive('mail')
                  ->once()
                  ->with(\Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any(), \Mockery::any());

        $transport->send($message);
    }

    public function testMessageHeadersOnlyHavePHPEolsDuringSending()
    {
        $dispatcher = $this->_createEventDispatcher();
        $transport = $this->_createTransport($dispatcher);

        $subject = $this->_createHeader();
        $subject->shouldReceive('getFieldBody')->andReturn("Foo\r\nBar");

        $headers = $this->_createHeaders(
            array(
                'Subject' => $subject,
            )
        );
        $message = $this->_createMessage($headers);
        $message->shouldReceive('toString')
                ->zeroOrMoreTimes()
                ->andReturn(
                    "From: Foo\r\n<foo@bar>\r\n" .
                    "\r\n" .
                    "This\r\n" .
                    'body'
                );

        if ("\r\n" != PHP_EOL) {
            $expectedHeaders = "From: Foo\n<foo@bar>\n";
            $expectedSubject = "Foo\nBar";
            $expectedBody = "This\nbody";
        } else {
            $expectedHeaders = "From: Foo\r\n<foo@bar>\r\n";
            $expectedSubject = "Foo\r\nBar";
            $expectedBody = "This\r\nbody";
        }

        $transport->shouldReceive('mail')
                  ->once()
                  ->with(\Mockery::any(), $expectedSubject, $expectedBody, $expectedHeaders, \Mockery::any());

        $transport->send($message);
    }

    // -- Creation Methods

    private function _createTransport($dispatcher)
    {
        return \Mockery::mock('Swift_Transport_MailTransport', array($dispatcher))->makePartial();
    }

    private function _createEventDispatcher()
    {
        return $this->getMockery('Swift_Events_EventDispatcher')->shouldIgnoreMissing();
    }

    private function _createMessage($headers)
    {
        $message = $this->getMockery('Swift_Mime_Message')->shouldIgnoreMissing();
        $message->shouldReceive('getHeaders')
                ->zeroOrMoreTimes()
                ->andReturn($headers);

        return $message;
    }

    private function _createHeaders($headers = array())
    {
        $set = $this->getMockery('Swift_Mime_HeaderSet')->shouldIgnoreMissing();

        if (count($headers) > 0) {
            foreach ($headers as $name => $header) {
                $set->shouldReceive('get')
                    ->zeroOrMoreTimes()
                    ->with($name)
                    ->andReturn($header);
                $set->shouldReceive('has')
                    ->zeroOrMoreTimes()
                    ->with($name)
                    ->andReturn(true);
            }
        }

        $header = $this->_createHeader();
        $set->shouldReceive('get')
            ->zeroOrMoreTimes()
            ->andReturn($header);
        $set->shouldReceive('has')
            ->zeroOrMoreTimes()
            ->andReturn(true);

        return $set;
    }

    private function _createHeader()
    {
        return $this->getMockery('Swift_Mime_Header')->shouldIgnoreMissing();
    }
}
