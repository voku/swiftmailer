<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Bridge for email-validation.
 */
class Swift_EmailValidatorBridge extends voku\helper\EmailCheck implements Swift_EmailValidatorInterface
{
    /**
     * @param $email
     *
     * @return bool
     */
    public function isValidWrapper($email)
    {
        return self::isValid($email);
    }

    /**
     * @param $email
     *
     * @return bool
     */
    public function isValidSimpleWrapper($email)
    {
        return (boolean)preg_match('/^(.*<?)(.*)@(.*)(>?)$/', $email);
    }
}
