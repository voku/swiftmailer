<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Handles Base64 (B) Header Encoding in Swift Mailer.
 *
 * @author Chris Corbyn
 */
class Swift_Mime_HeaderEncoder_Base64HeaderEncoder extends Swift_Encoder_Base64Encoder implements Swift_Mime_HeaderEncoder
{
    /**
     * Get the name of this encoding scheme.
     * Returns the string 'B'.
     *
     * @return string
     */
    public function getName()
    {
        return 'B';
    }

    /**
     * Takes an unencoded string and produces a Base64 encoded string from it.
     *
     * If the charset is iso-2022-jp, it uses mb_encode_mimeheader instead of
     * default encodeString, otherwise pass to the parent method.
     *
     * @param string $string        string to encode
     * @param int    $firstLineOffset
     * @param int    $maxLineLength optional, 0 indicates the default of 76 bytes
     * @param string $charset
     *
     * @return string
     */
    public function encodeString($string, $firstLineOffset = 0, $maxLineLength = 0, $charset = 'utf-8')
    {
        if (Swift::strtolowerWithStaticCache($charset) === 'iso-2022-jp') {
            $old = \mb_internal_encoding();

            \mb_internal_encoding('utf-8');
            $newString = \mb_encode_mimeheader($string, $charset, $this->getName(), "\r\n");

            \mb_internal_encoding($old);

            return $newString;
        }

        // safety measure copy-pasted from parent method
        if (0 >= $maxLineLength || 76 < $maxLineLength) {
            $maxLineLength = 76;
        }

        $cursorPosition = 0;
        $encoded = '';
        while ($cursorPosition < strlen($string)) {
            $maxChunkLength = $this->maxChunkLength($firstLineOffset, $maxLineLength);
            if ($cursorPosition > 0 || $firstLineOffset > $maxChunkLength) {
                $encoded .= "\r\n";
                $maxChunkLength = $this->maxChunkLength(0, $maxLineLength);
            }
            $chunk = mb_strcut($string, $cursorPosition, $maxChunkLength);
            $encoded .= base64_encode($chunk);
            $cursorPosition += strlen($chunk);
        }

        return $encoded;
    }

    /**
     * Returns maximum number of bytes that can fit in a line with given
     * offset and maximum length if encoded with base64
     *
     * @param int $firstLineOffset
     * @param int $maxLineLength
     *
     * @return int
     */
    private function maxChunkLength($firstLineOffset, $maxLineLength)
    {
        return floor(($maxLineLength - $firstLineOffset) / 4) * 3;
    }
}
