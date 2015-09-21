<?php

/*
 * This file is part of SwiftMailer.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Utility Class allowing users to simply check expressions again Swift EmailValidatorBridge.
 *
 * @author  Xavier De Cock <xdecock@gmail.com>
 */
class Swift_Validate
{
    /**
     * @var Swift_EmailValidatorBridge
     */
    private static $_emailValidator = null;


    /**
     * Checks if an e-mail address is valid.
     *
     * @param string $email
     *
     * @return bool
     */
    public static function email($email)
    {
        if (self::$_emailValidator === null) {
            self::$_emailValidator = new Swift_EmailValidatorBridge();
        }

        return self::$_emailValidator->isValidWrapper($email);
    }
}
