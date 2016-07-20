<?php

class Swift_Events_CommandEventTest extends \PHPUnit_Framework_TestCase
{
    public function testCommandCanBeFetchedByGetter()
    {
        $evt = $this->_createEvent($this->_createTransport(), "FOO\r\n");
        $this->assertSame("FOO\r\n", $evt->getCommand());
    }

    public function testSuccessCodesCanBeFetchedViaGetter()
    {
        $evt = $this->_createEvent($this->_createTransport(), "FOO\r\n", array(250));
        $this->assertSame(array(250), $evt->getSuccessCodes());
    }

    public function testSourceIsBuffer()
    {
        $transport = $this->_createTransport();
        $evt = $this->_createEvent($transport, "FOO\r\n");
        $ref = $evt->getSource();
        $this->assertSame($transport, $ref);
    }

    // -- Creation Methods

    private function _createEvent(Swift_Transport $source, $command, $successCodes = array())
    {
        return new Swift_Events_CommandEvent($source, $command, $successCodes);
    }

    /**
     * @return Swift_Transport|PHPUnit_Framework_MockObject_MockObject
     */
    private function _createTransport()
    {
        return $this->getMock('Swift_Transport');
    }
}
