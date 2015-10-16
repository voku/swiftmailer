<?php

class Swift_Bug666Test extends \PHPUnit_Framework_TestCase
{
    private $_attFile_1;
    private $_attFileName_1;
    private $_attFileType_1;

    private $_attFile_2;
    private $_attFileName_2;
    private $_attFileType_2;

    public function setUp()
    {
        $this->_attFileName_1 = 'data.txt';
        $this->_attFileType_1 = 'text/plain';
        $this->_attFile_1 = __DIR__.'/../../_samples/files/data.txt';

        $this->_attFileName_2 = 'spacer.gif';
        $this->_attFileType_2 = 'image/gif';
        $this->_attFile_2 = __DIR__.'/../../_samples/files/spacer.gif';

        Swift_Preferences::getInstance()->setCharset('utf-8');
    }

    public function testWritingMessageToByteStreamTwiceUsingAFileAttachment()
    {
        $message = new Swift_Message();
        $message->setSubject('test subject');
        $message->setTo('user@domain.tld');
        $message->setCc('other@domain.tld');
        $message->setFrom('user@domain.tld');

        $attachment = Swift_Attachment::fromPath($this->_attFile_1);
        $message->attach($attachment);

        $attachment = Swift_Attachment::fromPath($this->_attFile_2);
        $message->attach($attachment);

        $message->setBody('HTML part', 'text/html');

        $id = $message->getId();
        $date = preg_quote(date('r', $message->getDate()), '~');
        $boundary = $message->getBoundary();

        $streamA = new Swift_ByteStream_ArrayByteStream();
        $streamB = new Swift_ByteStream_ArrayByteStream();

        $pattern = '~^'.
                   'Message-ID: <'.$id.'>'."\r\n".
                   'Date: '.$date."\r\n".
                   'Subject: test subject'."\r\n".
                   'From: user@domain.tld'."\r\n".
                   'To: user@domain.tld'."\r\n".
                   'Cc: other@domain.tld'."\r\n".
                   'MIME-Version: 1.0'."\r\n".
                   'Content-Type: multipart/mixed;'."\r\n".
                   ' boundary="'.$boundary.'"'."\r\n".
                   "\r\n\r\n".
                   '--'.$boundary."\r\n".
                   'Content-Type: text/html; charset=utf-8'."\r\n".
                   'Content-Transfer-Encoding: quoted-printable'."\r\n".
                   "\r\n".
                   'HTML part'.
                   "\r\n\r\n".
                   '--'.$boundary."\r\n".
                   'Content-Type: '.$this->_attFileType_1.'; name='.$this->_attFileName_1."\r\n".
                   'Content-Transfer-Encoding: base64'."\r\n".
                   'Content-Disposition: attachment; filename='.$this->_attFileName_1."\r\n".
                   "\r\n".
                   preg_quote(base64_encode(file_get_contents($this->_attFile_1)), '~').
                   "\r\n\r\n".
                   '--'.$boundary.''."\r\n".
                   'Content-Type: '.$this->_attFileType_2.'; name='.$this->_attFileName_2."\r\n".
                   'Content-Transfer-Encoding: base64'."\r\n".
                   'Content-Disposition: attachment; filename='.$this->_attFileName_2."\r\n".
                   "\r\n".
                   preg_quote(base64_encode(file_get_contents($this->_attFile_2)), '~').
                   "\r\n\r\n".
                   '--'.$boundary.'--'."\r\n".
                   '$~D'
        ;

        $message->toByteStream($streamA);
        $message->toByteStream($streamB);

        $this->assertPatternInStream($pattern, $streamA);
        $this->assertPatternInStream($pattern, $streamB);
    }

    // -- Helpers

    public function assertPatternInStream($pattern, $stream, $message = '%s')
    {

        $string = '';
        while (false !== $bytes = $stream->read(8192)) {
            $string .= $bytes;
        }

        $this->assertRegExp($pattern, $string, $message);
    }
}
