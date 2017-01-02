<?php

/**
 * Class Swift_BugCve201610074
 *
 * @link https://legalhackers.com/advisories/SwiftMailer-Exploit-Remote-Code-Exec-CVE-2016-10074-Vuln.html
 */
class Swift_BugCve201610074 extends \PHPUnit_Framework_TestCase
{
    public function testEmbeddedImagesAreEmbedded()
    {
        // Attacker's input coming from untrusted source such as $_GET , $_POST etc.
        // For example from a Contact form with sender/body fields
        $email_from = '"attacker\"\ -oQ/tmp/\ -X/var/www/cache/phpcode.php\ "@email.com';
        $msg_body = '<?php phpinfo(); ?>';

        // Mail transport
        $transport = Swift_MailTransport::newInstance();
        // Create the Mailer using your created Transport
        $mailer = Swift_Mailer::newInstance($transport);

        // Create a message
        $message = Swift_Message::newInstance('Swift PoC exploit')
            ->setFrom(array($email_from => 'PoC Exploit Payload'))
            ->setTo(array('receiver@domain.org', 'other@domain.org' => 'A name'))
            ->setBody($msg_body);
        // Send the message with PoC payload in 'from' field
        $result = $mailer->send($message);

        self::assertSame(0, $result);

        // ---

        self::assertSame(null, $this->invokeMethod($transport, '_formatExtraParams', array('-f%s', $email_from))); // CVE
        self::assertSame(null, $this->invokeMethod($transport, '_formatExtraParams', array('-f%s', '"foo lall"@bar.com'))); // false positive v1
        self::assertSame(null, $this->invokeMethod($transport, '_formatExtraParams', array('-f%s', '"foo\ lall"@bar.com'))); // false positive v2
        self::assertSame('-ffoo@bar.com', $this->invokeMethod($transport, '_formatExtraParams', array('-f%s', 'foo@bar.com')));
        self::assertSame('-bs', $this->invokeMethod($transport, '_formatExtraParams', array('-bs', $email_from))); // http://swiftmailer.org/docs/sending.html
        self::assertSame('%s', $this->invokeMethod($transport, '_formatExtraParams', array('%s', $email_from)));
        self::assertSame(null, $this->invokeMethod($transport, '_formatExtraParams', array('', 'foo@bar.com')));
        self::assertSame(null, $this->invokeMethod($transport, '_formatExtraParams', array('-f%s', '')));
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
