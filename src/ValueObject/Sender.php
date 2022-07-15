<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * https://www.d3data.de
 *
 * @copyright (C) D3 Data Development (Inh. Thomas Dartsch)
 * @author    D3 Data Development - Daniel Seifert <support@shopmodule.com>
 * @link      https://www.oxidmodule.com
 */

declare(strict_types=1);

namespace D3\LinkmobilityClient\ValueObject;

use Assert\Assert;
use D3\LinkmobilityClient\Exceptions\ExceptionMessages;
use D3\LinkmobilityClient\Exceptions\NoSenderDefinedException;
use D3\LinkmobilityClient\Exceptions\RecipientException;
use D3\LinkmobilityClient\LoggerHandler;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class Sender extends ValueObject
{
    /**
     * @param string $number
     * @param string $iso2CountryCode
     *
     * @throws RecipientException
     * @throws NumberParseException
     */
    public function __construct(string $number = null, string $iso2CountryCode = null)
    {
        try {
            if (is_null($number) || is_null($iso2CountryCode)) {
                throw new NoSenderDefinedException();
            }

            Assert::that($iso2CountryCode)->string()->length(2);

            $phoneUtil = $this->getPhoneNumberUtil();

            $phoneNumber = $phoneUtil->parse($number, strtoupper($iso2CountryCode));
            $number      = $phoneUtil->format($phoneNumber, PhoneNumberFormat::E164);

            if (false === $phoneUtil->isValidNumber($phoneNumber)) {
                throw new RecipientException(ExceptionMessages::INVALID_SENDER);
            }

            parent::__construct($number);
        } catch (NoSenderDefinedException $e) {
            LoggerHandler::getInstance()->getLogger()->debug(
                ExceptionMessages::DEBUG_NOSENDERORCOUNTRYCODE
            );
        }
    }

    /**
     * @return PhoneNumberUtil
     */
    protected function getPhoneNumberUtil(): PhoneNumberUtil
    {
        return PhoneNumberUtil::getInstance();
    }

    public function getFormatted()
    {
        return ltrim(parent::getFormatted(), '+');
    }
}
