<?php

class Swift_Transport_StreamBufferTest extends \PHPUnit_Framework_TestCase
{
    public function testSettingWriteTranslationsCreatesFilters()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->once())
                ->method('createFilter')
                ->with('a', 'b')
                ->will($this->returnCallback(array($this, '_createFilter')));

        $buffer = $this->_createBuffer($factory);
        $buffer->setWriteTranslations(array('a' => 'b'));
    }

    public function testOverridingTranslationsOnlyAddsNeededFilters()
    {
        $factory = $this->_createFactory();
        $factory->expects($this->exactly(2))
                ->method('createFilter')
                ->will($this->returnCallback(array($this, '_createFilter')));

        $buffer = $this->_createBuffer($factory);
        $buffer->setWriteTranslations(array('a' => 'b'));
        $buffer->setWriteTranslations(array('x' => 'y', 'a' => 'b'));
    }

    // -- Creation methods

    private function _createBuffer($replacementFactory)
    {
        return new Swift_Transport_StreamBuffer($replacementFactory);
    }

    /**
     * @return Swift_ReplacementFilterFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private function _createFactory()
    {
        return $this->getMockBuilder('Swift_ReplacementFilterFactory')->getMock();
    }

    /**
     * @return Swift_StreamFilter|PHPUnit_Framework_MockObject_MockObject
     */
    public function _createFilter()
    {
        return $this->getMockBuilder('Swift_StreamFilter')->getMock();
    }
}
