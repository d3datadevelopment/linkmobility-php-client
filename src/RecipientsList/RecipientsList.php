<?php

/**
 * This Software is the property of Data Development and is protected
 * by copyright law - it is NOT Freeware.
 * Any unauthorized use of this software without a valid license
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 * http://www.shopmodule.com
 *
 * @copyright (C) D3 Data Development (Inh. Thomas Dartsch)
 * @author        D3 Data Development - Daniel Seifert <support@shopmodule.com>
 * @link          http://www.oxidmodule.com
 */

declare(strict_types=1);

namespace D3\LinkmobilityClient\RecipientsList;

use D3\LinkmobilityClient\ValueObject\Recipient;
use libphonenumber\PhoneNumberType;

class RecipientsList implements RecipientsListInterface, \Iterator
{
    /**
     * @var array
     */
    private $recipients = [];

    public function add(Recipient $recipient) : RecipientsListInterface
    {
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        try {
            $phoneNumber = $phoneUtil->parse($recipient->get(), $recipient->getCountryCode());

            if (false === $phoneUtil->isValidNumber($phoneNumber)) {
                throw new \D3\LinkmobilityClient\Exceptions\RecipientException('invalid recipient phone number');
            } elseif (false === in_array($phoneUtil->getNumberType($phoneNumber), [PhoneNumberType::MOBILE, PhoneNumberType::FIXED_LINE_OR_MOBILE])) {
                throw new \D3\LinkmobilityClient\Exceptions\RecipientException('not a mobile number');
            }

            $this->recipients[md5(serialize($recipient))] = $recipient;
        } catch (\libphonenumber\NumberParseException $e) {
//            var_dump($e);
        } catch (\D3\LinkmobilityClient\Exceptions\RecipientException $e) {
//            var_dump($e);
        }
        
        return $this;
    }

    public function clearRecipents() : RecipientsListInterface
    {
        $this->recipients = [];

        return $this;
    }

    public function getRecipients() : array
    {
        return array_values(
            array_map(
                function (Recipient $recipient) {
                    return $recipient->get();
                },
                $this->recipients
            )
        );
    }

    public function getRecipientsList() : array
    {
        return $this->recipients;
    }

    public function current()
    {
        return current($this->recipients);
    }

    public function next()
    {
        return next($this->recipients);
    }

    public function key()
    {
        return key($this->recipients);
    }

    public function rewind()
    {
        return reset($this->recipients);
    }

    public function valid()
    {
        return (false !== current($this->recipients) && current($this->recipients) instanceof Recipient);
    }

    //stract methods and must therefore be declared abstract or implement the remaining methods (Iterator::current, Iterator::next, Iterator::key, ...) in
}