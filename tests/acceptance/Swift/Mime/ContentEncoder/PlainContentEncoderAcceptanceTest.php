<?php

/**
 * Class Swift_Mime_ContentEncoder_PlainContentEncoderAcceptanceTest
 */
class Swift_Mime_ContentEncoder_PlainContentEncoderAcceptanceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $_samplesDir;

    /**
     * @var Swift_Mime_ContentEncoder_PlainContentEncoder
     */
    private $_encoder;

    protected function setUp()
    {
        /** @noinspection RealpathOnRelativePathsInspection */
        $this->_samplesDir = realpath(__DIR__.'/../../../../_samples/charsets');

        $this->_encoder = new Swift_Mime_ContentEncoder_PlainContentEncoder('8bit');
    }

    public function testEncodingAndDecodingSamplesString()
    {
        $sampleFp = opendir($this->_samplesDir);
        while (false !== $encodingDir = readdir($sampleFp)) {
            if (0 === strpos($encodingDir, '.')) {
                continue;
            }

            $sampleDir = $this->_samplesDir.'/'.$encodingDir;

            if (is_dir($sampleDir)) {
                $fileFp = opendir($sampleDir);
                while (false !== $sampleFile = readdir($fileFp)) {
                    if (0 === strpos($sampleFile, '.')) {
                        continue;
                    }

                    $text = file_get_contents($sampleDir.'/'.$sampleFile);
                    $encodedText = $this->_encoder->encodeString($text);

                    $this->assertSame(
                        $encodedText,
                        $text,
                        '%s: Encoded string should be identical to original string for sample '. $sampleDir.'/'.$sampleFile
                    );
                }
                closedir($fileFp);
            }
        }
        closedir($sampleFp);
    }

    public function testEncodingAndDecodingSamplesByteStream()
    {
        $sampleFp = opendir($this->_samplesDir);
        while (false !== $encodingDir = readdir($sampleFp)) {
            if (0 === strpos($encodingDir, '.')) {
                continue;
            }

            $sampleDir = $this->_samplesDir.'/'.$encodingDir;

            if (is_dir($sampleDir)) {
                $fileFp = opendir($sampleDir);
                while (false !== $sampleFile = readdir($fileFp)) {
                    if (0 === strpos($sampleFile, '.')) {
                        continue;
                    }

                    $text = file_get_contents($sampleDir.'/'.$sampleFile);

                    $os = new Swift_ByteStream_ArrayByteStream();
                    $os->write($text);

                    $is = new Swift_ByteStream_ArrayByteStream();

                    $this->_encoder->encodeByteStream($os, $is);

                    $encoded = '';
                    while (false !== $bytes = $is->read(8192)) {
                        $encoded .= $bytes;
                    }

                    $this->assertSame(
                        $encoded,
                        $text,
                        '%s: Encoded string should be identical to original string for sample '. $sampleDir.'/'.$sampleFile
                    );
                }
                closedir($fileFp);
            }
        }
        closedir($sampleFp);
    }
}
