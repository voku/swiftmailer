<?php

use voku\helper\UTF8;

/**
 * A CharacterStream implementation which skips over all the manual processing
 *  performed by NgCharacterStream.
 *
 * @package Swift
 * @author  mappu
 */
class Swift_CharacterStream_MbCharacterStream implements Swift_CharacterStream
{

    /**
     * @var string
     */
    private $_charset = 'utf-8';

    /**
     * @var int
     */
    private $_strpos = 0;

    /**
     * @var string
     */
    private $_buffer = '';

    /**
     * @var int
     */
    private $_strlen = 0;

    public function flushContents()
    {
        $this->_strpos = 0;
        $this->_buffer = '';
    }

    /**
     * @param Swift_OutputByteStream $os
     */
    public function importByteStream(\Swift_OutputByteStream $os)
    {
        $this->flushContents();
        $blocks = 512;
        $os->setReadPointer(0);
        while (($read = $os->read($blocks)) !== false) {
            $this->write($read);
        }
    }

    /**
     * @param string $string
     */
    public function importString($string)
    {
        $this->flushContents();
        $this->write($string);
    }

    /**
     * @param int $length
     *
     * @return false|string
     */
    public function read($length)
    {
        if ($this->_strpos >= $this->_strlen) {
            return false;
        }

        $readChars = min($length, $this->_strlen - $this->_strpos);

        $ret = UTF8::substr($this->_buffer, $this->_strpos, $readChars, $this->_charset);

        $this->_strpos += $readChars;

        return $ret;
    }

    /**
     * @param int $length
     *
     * @return int[]|bool
     */
    public function readBytes($length)
    {
        $read = $this->read($length);

        if ($read !== false) {
            return array_map('ord', str_split($read, 1));
        } else {
            return false;
        }
    }

    /**
     * @param Swift_CharacterReaderFactory $factory
     */
    public function setCharacterReaderFactory(\Swift_CharacterReaderFactory $factory)
    {
        // Ignore
    }

    /**
     * @param string $charset
     */
    public function setCharacterSet($charset)
    {
        if ($charset) {
            $this->_charset = $charset;
        }
    }

    /**
     * @param int $charOffset
     */
    public function setPointer($charOffset)
    {
        $this->_strpos = $charOffset;
    }

    /**
     * @param string $chars
     */
    public function write($chars)
    {
        $this->_buffer .= $chars;
        $this->_strlen += UTF8::strlen($chars, $this->_charset);
    }
}
