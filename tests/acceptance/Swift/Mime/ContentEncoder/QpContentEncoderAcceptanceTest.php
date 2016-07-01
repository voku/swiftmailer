<?php

use voku\helper\UTF8;

class Swift_Mime_ContentEncoder_QpContentEncoderAcceptanceTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @var string
   */
  private $_samplesDir;

  /**
   * @var Swift_CharacterReaderFactory_SimpleCharacterReaderFactory
   */
  private $_factory;

  public function setUp()
  {
    /** @noinspection RealpathOnRelativePathsInspection */
    $this->_samplesDir = realpath(__DIR__ . '/../../../../_samples/charsets');
    $this->_factory = new Swift_CharacterReaderFactory_SimpleCharacterReaderFactory();
  }

  public function tearDown()
  {
    Swift_Preferences::getInstance()->setQPDotEscape(false);
  }

  public function testEncodingAndDecodingSamples()
  {
    $sampleFp = opendir($this->_samplesDir);
    while (false !== $encodingDir = readdir($sampleFp)) {
      if (0 === strpos($encodingDir, '.')) {
        continue;
      }

      $encoding = $encodingDir;
      $charStream = new Swift_CharacterStream_NgCharacterStream($this->_factory, $encoding);
      $encoder = new Swift_Mime_ContentEncoder_QpContentEncoder($charStream);

      $sampleDir = $this->_samplesDir . '/' . $encodingDir;

      if (is_dir($sampleDir)) {
        $fileFp = opendir($sampleDir);
        while (false !== $sampleFile = readdir($fileFp)) {
          if (0 === strpos($sampleFile, '.')) {
            continue;
          }

          $text = UTF8::file_get_contents($sampleDir . '/' . $sampleFile);

          $os = new Swift_ByteStream_ArrayByteStream();
          $os->write($text);

          $is = new Swift_ByteStream_ArrayByteStream();
          $encoder->encodeByteStream($os, $is);

          $encoded = '';
          while (false !== $bytes = $is->read(8192)) {
            $encoded .= $bytes;
          }

          self::assertEquals(
              $text,
              quoted_printable_decode($encoded),
              '%s: Encoded string should decode back to original string for sample ' . $sampleDir . '/' . $sampleFile
          );
        }
        closedir($fileFp);
      }
    }
    closedir($sampleFp);
  }

  public function testEncodingAndDecodingSamplesFromDiConfiguredInstance()
  {
    $sampleFp = opendir($this->_samplesDir);
    while (false !== $encodingDir = readdir($sampleFp)) {
      if (0 === strpos($encodingDir, '.')) {
        continue;
      }

      $encoder = $this->_createEncoderFromContainer();
      $sampleDir = $this->_samplesDir . '/' . $encodingDir;

      if (is_dir($sampleDir)) {
        $fileFp = opendir($sampleDir);
        while (false !== $sampleFile = readdir($fileFp)) {
          if (0 === strpos($sampleFile, '.')) {
            continue;
          }

          $text = file_get_contents($sampleDir . '/' . $sampleFile);

          $os = new Swift_ByteStream_ArrayByteStream();
          $os->write($text);

          $is = new Swift_ByteStream_ArrayByteStream();
          $encoder->encodeByteStream($os, $is);

          $encoded = '';
          while (false !== $bytes = $is->read(512)) {
            $encoded .= $bytes;
          }

          $decoded = quoted_printable_decode($encoded);

          self::assertEquals(
              0,
              strcmp(
                str_replace("\r\n", "\n", $decoded),
                str_replace("\r\n", "\n", $text)
              ),
              '%s: Encoded string should decode back to original string for sample ' . $sampleDir . '/' . $sampleFile .
              ' | ' . "\n\n" .
              $decoded . "\n\n" .
              'last char: (' . UTF8::single_chr_html_encode(UTF8::substr($decoded, -1)) . ')' . "\n\n" .
              ' |---| ' . "\n\n" .
              $text . "\n\n" .
              'last char: (' . UTF8::single_chr_html_encode(UTF8::substr($text, -1)) . ')' . "\n\n\n\n"
          );
        }
        closedir($fileFp);
      }
    }
    closedir($sampleFp);
  }

  public function testEncodingLFTextWithDiConfiguredInstance()
  {
    $encoder = $this->_createEncoderFromContainer();
    self::assertEquals("a\r\nb\r\nc", $encoder->encodeString("a\nb\nc"));
  }

  public function testEncodingCRTextWithDiConfiguredInstance()
  {
    $encoder = $this->_createEncoderFromContainer();
    self::assertEquals("a\r\nb\r\nc", $encoder->encodeString("a\rb\rc"));
  }

  public function testEncodingLFCRTextWithDiConfiguredInstance()
  {
    $encoder = $this->_createEncoderFromContainer();
    self::assertEquals("a\r\n\r\nb\r\n\r\nc", $encoder->encodeString("a\n\rb\n\rc"));
  }

  public function testEncodingCRLFTextWithDiConfiguredInstance()
  {
    $encoder = $this->_createEncoderFromContainer();
    self::assertEquals("a\r\nb\r\nc", $encoder->encodeString("a\r\nb\r\nc"));
  }

  public function testEncodingDotStuffingWithDiConfiguredInstance()
  {
    // Enable DotEscaping
    Swift_Preferences::getInstance()->setQPDotEscape(true);
    $encoder = $this->_createEncoderFromContainer();
    self::assertEquals("a=2E\r\n=2E\r\n=2Eb\r\nc", $encoder->encodeString("a.\r\n.\r\n.b\r\nc"));

    // Return to default
    Swift_Preferences::getInstance()->setQPDotEscape(false);
    $encoder = $this->_createEncoderFromContainer();
    self::assertEquals("a.\r\n.\r\n.b\r\nc", $encoder->encodeString("a.\r\n.\r\n.b\r\nc"));
  }

  public function testDotStuffingEncodingAndDecodingSamplesFromDiConfiguredInstance()
  {
    // Enable DotEscaping
    Swift_Preferences::getInstance()->setQPDotEscape(true);
    $this->testEncodingAndDecodingSamplesFromDiConfiguredInstance();
  }

  private function _createEncoderFromContainer()
  {
    return Swift_DependencyContainer::getInstance()->lookup('mime.qpcontentencoder');
  }
}
