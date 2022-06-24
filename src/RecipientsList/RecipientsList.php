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

use D3\LinkmobilityClient\Exceptions\ExceptionMessages;
use D3\LinkmobilityClient\Exceptions\RecipientException;
use D3\LinkmobilityClient\ValueObject\Recipient;
use Iterator;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;

class RecipientsList implements RecipientsListInterface, Iterator
{
    /**
     * @var array
     */
    private $recipients = [];

    public function add(Recipient $recipient) : RecipientsListInterface
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $phoneNumber = $phoneUtil->parse( $recipient->get(), $recipient->getCountryCode() );

            if ( false === $phoneUtil->isValidNumber( $phoneNumber ) ) {
                throw new RecipientException( ExceptionMessages::INVALID_RECIPIENT_PHONE );
            } elseif (
                false === in_array(
                    $phoneUtil->getNumberType( $phoneNumber ),
                    [
                        PhoneNumberType::MOBILE,
                        PhoneNumberType::FIXED_LINE_OR_MOBILE
                    ]
                )
            ) {
                throw new RecipientException( ExceptionMessages::NOT_A_MOBILE_NUMBER );
            }

            $this->recipients[ md5( serialize( $recipient ) ) ] = $recipient;
        } catch (NumberParseException $e) {
//            var_dump($e);
        } catch (RecipientException $e) {
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

    /**
     * @return array
     */
    public function getRecipientsList() : array
    {
        return $this->recipients;
    }

    /**
     * @return false|mixed
     */
    public function current()
    {
        return current($this->recipients);
    }

    /**
     * @return false|mixed|void
     */
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

    public function valid(): bool
    {
        return current($this->recipients) instanceof Recipient;
    }
}