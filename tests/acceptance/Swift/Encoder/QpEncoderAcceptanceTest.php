<?php

use voku\helper\UTF8;

/**
 * Class Swift_Encoder_QpEncoderAcceptanceTest
 */
class Swift_Encoder_QpEncoderAcceptanceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $_samplesDir;

    /**
     * @var Swift_CharacterReaderFactory_SimpleCharacterReaderFactory
     */
    private $_factory;

    protected function setUp()
    {
        /** @noinspection RealpathOnRelativePathsInspection */
        $this->_samplesDir = realpath(__DIR__ . '/../../../_samples/charsets');

        $this->_factory = new Swift_CharacterReaderFactory_SimpleCharacterReaderFactory();
    }

    public function testEncodingAndDecodingSamples()
    {
        $sampleFp = opendir($this->_samplesDir);
        while (false !== $encodingDir = readdir($sampleFp)) {
            if (0 === strpos($encodingDir, '.')) {
                continue;
            }

            $encoding = $encodingDir;
            $charStream = new Swift_CharacterStream_ArrayCharacterStream(
                $this->_factory, $encoding
            );
            $encoder = new Swift_Encoder_QpEncoder($charStream);

            $sampleDir = $this->_samplesDir . '/' . $encodingDir;

            if (is_dir($sampleDir)) {
                $fileFp = opendir($sampleDir);
                while (false !== $sampleFile = readdir($fileFp)) {
                    if (0 === strpos($sampleFile, '.')) {
                        continue;
                    }

                    $text = UTF8::file_get_contents($sampleDir . '/' . $sampleFile);
                    $encodedText = $encoder->encodeString($text);

                    foreach (explode("\r\n", $encodedText) as $line) {
                        $this->assertLessThanOrEqual(2507, strlen($line));
                    }

                    $this->assertSame(
                        $text,
                        quoted_printable_decode($encodedText),
                        '%s: Encoded string should decode back to original string for sample ' . $sampleDir . '/' . $sampleFile
                    );
                }
                closedir($fileFp);
            }
        }
        closedir($sampleFp);
    }
}
