<?php

class Swift_Plugins_RedirectingPluginTest extends \PHPUnit_Framework_TestCase
{
    public function testRecipientCanBeSetAndFetched()
    {
        $plugin = new Swift_Plugins_RedirectingPlugin('fabien@example.com');
        $this->assertSame('fabien@example.com', $plugin->getRecipient());
        $plugin->setRecipient('chris@example.com');
        $this->assertSame('chris@example.com', $plugin->getRecipient());
    }

    public function testPluginChangesRecipients()
    {
        $message = Swift_Message::newInstance()
            ->setSubject('...')
            ->setFrom(array('john@example.com' => 'John Doe'))
            ->setTo($to = array(
                'fabien-to@example.com' => 'Fabien (To)',
                'chris-to@example.com' => 'Chris (To)',
            ))
            ->setCc($cc = array(
                'fabien-cc@example.com' => 'Fabien (Cc)',
                'chris-cc@example.com' => 'Chris (Cc)',
            ))
            ->setBcc($bcc = array(
                'fabien-bcc@example.com' => 'Fabien (Bcc)',
                'chris-bcc@example.com' => 'Chris (Bcc)',
            ))
            ->setBody('...')
        ;

        $plugin = new Swift_Plugins_RedirectingPlugin('god@example.com');

        $evt = $this->_createSendEvent($message);

        $plugin->beforeSendPerformed($evt);

        $this->assertSame($message->getTo(), array('god@example.com' => null));
        $this->assertSame($message->getCc(), array());
        $this->assertSame($message->getBcc(), array());

        $plugin->sendPerformed($evt);

        $this->assertSame($message->getTo(), $to);
        $this->assertSame($message->getCc(), $cc);
        $this->assertSame($message->getBcc(), $bcc);
    }

    public function testPluginRespectsUnsetToList()
    {
        $message = Swift_Message::newInstance()
            ->setSubject('...')
            ->setFrom(array('john@example.com' => 'John Doe'))
            ->setCc($cc = array(
                'fabien-cc@example.com' => 'Fabien (Cc)',
                'chris-cc@example.com' => 'Chris (Cc)',
            ))
            ->setBcc($bcc = array(
                'fabien-bcc@example.com' => 'Fabien (Bcc)',
                'chris-bcc@example.com' => 'Chris (Bcc)',
            ))
            ->setBody('...')
        ;

        $plugin = new Swift_Plugins_RedirectingPlugin('god@example.com');

        $evt = $this->_createSendEvent($message);

        $plugin->beforeSendPerformed($evt);

        $this->assertSame($message->getTo(), array('god@example.com' => null));
        $this->assertSame($message->getCc(), array());
        $this->assertSame($message->getBcc(), array());

        $plugin->sendPerformed($evt);

        $this->assertSame($message->getTo(), array());
        $this->assertSame($message->getCc(), $cc);
        $this->assertSame($message->getBcc(), $bcc);
    }

    public function testPluginRespectsAWhitelistOfPatterns()
    {
        $message = Swift_Message::newInstance()
            ->setSubject('...')
            ->setFrom(array('john@example.com' => 'John Doe'))
            ->setTo($to = array(
                'fabien-to@example.com' => 'Fabien (To)',
                'chris-to@example.com' => 'Chris (To)',
                'lars-to@internal.com' => 'Lars (To)',
            ))
            ->setCc($cc = array(
                'fabien-cc@example.com' => 'Fabien (Cc)',
                'chris-cc@example.com' => 'Chris (Cc)',
                'lars-cc@internal.org' => 'Lars (Cc)',
            ))
            ->setBcc($bcc = array(
                'fabien-bcc@example.com' => 'Fabien (Bcc)',
                'chris-bcc@example.com' => 'Chris (Bcc)',
                'john-bcc@example.org' => 'John (Bcc)',
            ))
            ->setBody('...')
        ;

        $recipient = 'god@example.com';
        $patterns = array('/^.*@internal.[a-z]+$/', '/^john-.*$/');

        $plugin = new Swift_Plugins_RedirectingPlugin($recipient, $patterns);

        $this->assertSame($recipient, $plugin->getRecipient());
        $this->assertSame($plugin->getWhitelist(), $patterns);

        $evt = $this->_createSendEvent($message);

        $plugin->beforeSendPerformed($evt);

        $this->assertSame($message->getTo(), array('lars-to@internal.com' => 'Lars (To)', 'god@example.com' => null));
        $this->assertSame($message->getCc(), array('lars-cc@internal.org' => 'Lars (Cc)'));
        $this->assertSame($message->getBcc(), array('john-bcc@example.org' => 'John (Bcc)'));

        $plugin->sendPerformed($evt);

        $this->assertSame($message->getTo(), $to);
        $this->assertSame($message->getCc(), $cc);
        $this->assertSame($message->getBcc(), $bcc);
    }

    public function testArrayOfRecipientsCanBeExplicitlyDefined()
    {
        $message = Swift_Message::newInstance()
            ->setSubject('...')
            ->setFrom(array('john@example.com' => 'John Doe'))
            ->setTo(array(
            'fabien@example.com' => 'Fabien',
            'chris@example.com' => 'Chris (To)',
            'lars-to@internal.com' => 'Lars (To)',
        ))
            ->setCc(array(
            'fabien@example.com' => 'Fabien',
            'chris-cc@example.com' => 'Chris (Cc)',
            'lars-cc@internal.org' => 'Lars (Cc)',
        ))
            ->setBcc(array(
            'fabien@example.com' => 'Fabien',
            'chris-bcc@example.com' => 'Chris (Bcc)',
            'john-bcc@example.org' => 'John (Bcc)',
        ))
            ->setBody('...')
        ;

        $recipients = array('god@example.com', 'fabien@example.com');
        $patterns = array('/^.*@internal.[a-z]+$/');

        $plugin = new Swift_Plugins_RedirectingPlugin($recipients, $patterns);

        $evt = $this->_createSendEvent($message);

        $plugin->beforeSendPerformed($evt);

        $this->assertSame(
            $message->getTo(),
            array('fabien@example.com' => 'Fabien', 'lars-to@internal.com' => 'Lars (To)', 'god@example.com' => null)
        );
        $this->assertSame(
            $message->getCc(),
            array('fabien@example.com' => 'Fabien', 'lars-cc@internal.org' => 'Lars (Cc)')
        );
        $this->assertSame($message->getBcc(), array('fabien@example.com' => 'Fabien'));
    }

    // -- Creation Methods

    /**
     * @param Swift_Mime_Message $message
     *
     * @return Swift_Events_SendEvent|PHPUnit_Framework_MockObject_MockObject
     */
    private function _createSendEvent(Swift_Mime_Message $message)
    {
        $evt = $this->getMockBuilder('Swift_Events_SendEvent')
                    ->disableOriginalConstructor()
                    ->getMock();
        $evt->expects($this->any())
            ->method('getMessage')
            ->will($this->returnValue($message));

        return $evt;
    }
}
