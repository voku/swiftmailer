<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Sends Messages using the mail() function.
 *
 * It is advised that users do not use this transport if at all possible
 * since a number of plugin features cannot be used in conjunction with this
 * transport due to the internal interface in PHP itself.
 *
 * The level of error reporting with this transport is incredibly weak, again
 * due to limitations of PHP's internal mail() function.  You'll get an
 * all-or-nothing result from sending.
 *
 * @author Chris Corbyn
 */
class Swift_Transport_MailTransport implements Swift_Transport
{
    /** Additional parameters to pass to mail() */
    private $_extraParams = '-f%s';

    /** The event dispatcher from the plugin API */
    private $_eventDispatcher;

    /**
     * Create a new MailTransport with the $log.
     *
     * @param Swift_Events_EventDispatcher $eventDispatcher
     */
    public function __construct(Swift_Events_EventDispatcher $eventDispatcher)
    {
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * Not used.
     */
    public function isStarted()
    {
        return false;
    }

    /**
     * Not used.
     */
    public function start()
    {
    }

    /**
     * @return bool
     */
    public function ping()
    {
        return true;
    }

    /**
     * Not used.
     */
    public function stop()
    {
    }

    /**
     * Set the additional parameters used on the mail() function.
     *
     * This string is formatted for sprintf() where %s is the sender address.
     *
     * @param string $params
     *
     * @return $this
     */
    public function setExtraParams($params)
    {
        $this->_extraParams = $params;

        return $this;
    }

    /**
     * Get the additional parameters used on the mail() function.
     *
     * This string is formatted for sprintf() where %s is the sender address.
     *
     * @return string
     */
    public function getExtraParams()
    {
        return $this->_extraParams;
    }

    /**
     * Send the given Message.
     *
     * Recipient/sender data will be retrieved from the Message API.
     * The return value is the number of recipients who were accepted for delivery.
     *
     * @param Swift_Mime_Message $message
     * @param string[]           $failedRecipients An array of failures (by-reference)
     *
     * @return int
     *
     * @throws Swift_TransportException
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $failedRecipients = (array)$failedRecipients;

        $evt = $this->_eventDispatcher->createSendEvent($this, $message);
        if ($evt) {

            $this->_eventDispatcher->dispatchEvent($evt, 'beforeSendPerformed');
            if ($evt->bubbleCancelled()) {
                return 0;
            }
        }

        $count = (
            \count((array)$message->getTo())
            + \count((array)$message->getCc())
            + \count((array)$message->getBcc())
        );

        /*
        // TODO: check if we need this check, breaks "Mockery"-Tests
        if ($count === 0) {
            $this->_throwException(new Swift_TransportException('Cannot send message without a recipient'));
        }
        */

        $toHeader = $message->getHeaders()->get('To');
        $subjectHeader = $message->getHeaders()->get('Subject');

        if (0 === $count) {
            $this->_throwException(new Swift_TransportException('Cannot send message without a recipient'));
        }

        $to = $toHeader ? $toHeader->getFieldBody() : '';
        $subject = $subjectHeader ? $subjectHeader->getFieldBody() : '';

        $reversePath = $this->_getReversePath($message);

        // Remove headers that would otherwise be duplicated
        $message->getHeaders()->remove('To');
        $message->getHeaders()->remove('Subject');

        $messageStr = $message->toString();

        if ($toHeader) {
          $message->getHeaders()->set($toHeader);
        }
        $message->getHeaders()->set($subjectHeader);

        // Separate headers from body
        if (false !== $endHeaders = \strpos($messageStr, "\r\n\r\n")) {
            $headers = \substr($messageStr, 0, $endHeaders) . "\r\n"; // Keep last EOL
            $body = \substr($messageStr, $endHeaders + 4);
        } else {
            $headers = $messageStr . "\r\n";
            $body = '';
        }

        unset($messageStr);

        if ("\r\n" !== PHP_EOL) {
            // Non-windows (not using SMTP)
            $headers = \str_replace("\r\n", PHP_EOL, $headers);
            $subject = \str_replace("\r\n", PHP_EOL, $subject);
            $body = \str_replace("\r\n", PHP_EOL, $body);
            $to = \str_replace("\r\n", PHP_EOL, $to);
        } else {
            // Windows, using SMTP
            $headers = \str_replace("\r\n.", "\r\n..", $headers);
            $subject = \str_replace("\r\n.", "\r\n..", $subject);
            $body = \str_replace("\r\n.", "\r\n..", $body);
            $to = \str_replace("\r\n.", "\r\n..", $to);
        }

        if ($this->mail($to, $subject, $body, $headers, $this->_formatExtraParams($this->_extraParams, $reversePath))) {
            if ($evt) {
                $evt->setResult(Swift_Events_SendEvent::RESULT_SUCCESS);
                $evt->setFailedRecipients($failedRecipients);
                $this->_eventDispatcher->dispatchEvent($evt, 'sendPerformed');
            }
        } else {
            $failedRecipients = \array_merge(
                $failedRecipients,
                \array_keys((array)$message->getTo()),
                \array_keys((array)$message->getCc()),
                \array_keys((array)$message->getBcc())
            );

            if ($evt) {
                $evt->setResult(Swift_Events_SendEvent::RESULT_FAILED);
                $evt->setFailedRecipients($failedRecipients);
                $this->_eventDispatcher->dispatchEvent($evt, 'sendPerformed');
            }

            $count = 0;
        }

        $message->generateId();  // Make sure a new Message ID is used

        return $count;
    }

    /**
     * Register a plugin.
     *
     * @param Swift_Events_EventListener $plugin
     */
    public function registerPlugin(Swift_Events_EventListener $plugin)
    {
        $this->_eventDispatcher->bindEventListener($plugin);
    }

    /**
     * Throw a TransportException, first sending it to any listeners
     *
     * @param Swift_TransportException $e
     *
     * @throws Swift_TransportException
     */
    protected function _throwException(Swift_TransportException $e)
    {
        $evt = $this->_eventDispatcher->createTransportExceptionEvent($this, $e);
        if ($evt) {

            $this->_eventDispatcher->dispatchEvent($evt, 'exceptionThrown');
            if (!$evt->bubbleCancelled()) {
                throw $e;
            }

        } else {
            throw $e;
        }
    }

    /**
     * Send mail via the mail() function.
     *
     * This method takes the same arguments as PHP mail().
     *
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param string $headers
     * @param string $extraParams
     *
     * @return bool
     */
    public function mail($to, $subject, $body, $headers = null, $extraParams = null)
    {
        /** @noinspection DeprecatedIniOptionsInspection */
        if (!ini_get('safe_mode')) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            return @mail($to, $subject, $body, $headers, $extraParams);
        }

        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        return @mail($to, $subject, $body, $headers);
    }

    /**
     * Determine the best-use reverse path for this message
     *
     * @param Swift_Mime_Message $message
     *
     * @return mixed|null|string
     */
    private function _getReversePath(Swift_Mime_Message $message)
    {
        $return = $message->getReturnPath();
        $sender = $message->getSender();
        $from = $message->getFrom();
        $path = null;
        if (!empty($return)) {
            $path = $return;
        } elseif (!empty($sender)) {
            $keys = \array_keys($sender);
            $path = \array_shift($keys);
        } elseif (!empty($from)) {
            $keys = \array_keys($from);
            $path = \array_shift($keys);
        }

        return $path;
    }

    /**
     * Fix CVE-2016-10074 by disallowing potentially unsafe shell characters.
     *
     * Note that escapeshellarg and escapeshellcmd are inadequate for our purposes, especially on Windows.
     *
     * @param string $string The string to be validated
     *
     * @return bool
     */
    private function _isShellSafe($string)
    {
        // Future-proof
        if (
            \escapeshellcmd($string) !== $string
            ||
            !\in_array(\escapeshellarg($string), array("'$string'", "\"$string\""), true)
        ) {
            return false;
        }

        $length = \strlen($string);
        for ($i = 0; $i < $length; ++$i) {
            $c = $string[$i];
            // All other characters have a special meaning in at least one common shell, including = and +.
            // Full stop (.) has a special meaning in cmd.exe, but its impact should be negligible here.
            // Note that this does permit non-Latin alphanumeric characters based on the current locale.
            if (!\ctype_alnum($c) && \strpos('@_-.', $c) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return php mail extra params to use for invoker->mail.
     *
     * @param string $extraParams
     * @param string $reversePath
     *
     * @return null|string
     */
    private function _formatExtraParams($extraParams, $reversePath)
    {
        if (\strpos($extraParams, '-f%s') !== false) {
            if (
                empty($reversePath)
                ||
                false === $this->_isShellSafe($reversePath)
            ) {
                $extraParams = \str_replace('-f%s', '', $extraParams);
            } else {
                $extraParams = \sprintf($extraParams, $reversePath);
            }
        }

        return !empty($extraParams) ? $extraParams : null;
    }
}
