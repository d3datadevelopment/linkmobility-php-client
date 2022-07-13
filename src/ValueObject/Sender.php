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
use D3\LinkmobilityClient\Exceptions\RecipientException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class Sender extends StringValueObject
{
    /**
     * @param string $number
     * @param string $iso2CountryCode
     *
     * @throws RecipientException
     * @throws NumberParseException
     */
    public function __construct(string $number, string $iso2CountryCode)
    {
        Assert::that($iso2CountryCode)->string()->length(2);

        $phoneUtil = $this->getPhoneNumberUtil();

        $phoneNumber = $phoneUtil->parse($number, strtoupper($iso2CountryCode));
        $number = ltrim($phoneUtil->format($phoneNumber, PhoneNumberFormat::E164), '+');

        if (false === $phoneUtil->isValidNumber($phoneNumber)) {
            throw new RecipientException(ExceptionMessages::INVALID_SENDER);
        }

        parent::__construct($number);
    }

    /**
     * @return PhoneNumberUtil
     */
    protected function getPhoneNumberUtil(): PhoneNumberUtil
    {
        return PhoneNumberUtil::getInstance();
    }

    /**
     * @return int
     */
    public function getFormatted()
    {
        return parent::getFormatted();
    }
}
