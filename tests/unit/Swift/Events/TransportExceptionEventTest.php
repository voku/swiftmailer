<?php

class Swift_Events_TransportExceptionEventTest extends \PHPUnit_Framework_TestCase
{
    public function testExceptionCanBeFetchViaGetter()
    {
        $ex = $this->_createException();
        $transport = $this->_createTransport();
        $evt = $this->_createEvent($transport, $ex);
        $ref = $evt->getException();
        $this->assertSame($ex, $ref,
            '%s: Exception should be available via getException()'
            );
    }

    public function testSourceIsTransport()
    {
        $ex = $this->_createException();
        $transport = $this->_createTransport();
        $evt = $this->_createEvent($transport, $ex);
        $ref = $evt->getSource();
        $this->assertSame($transport, $ref,
            '%s: Transport should be available via getSource()'
            );
    }

    private function _createEvent(Swift_Transport $transport, Swift_TransportException $ex)
    {
        return new Swift_Events_TransportExceptionEvent($transport, $ex);
    }

    /**
     * @return Swift_Transport|PHPUnit_Framework_MockObject_MockObject
     */
    private function _createTransport()
    {
        return $this->getMockBuilder('Swift_Transport')->getMock();
    }

    private function _createException()
    {
        return new Swift_TransportException('');
    }
}
