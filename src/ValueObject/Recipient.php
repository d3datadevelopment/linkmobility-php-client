<?php

declare(strict_types=1);

namespace D3\LinkmobilityClient\ValueObject;

use Assert\Assert;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class Recipient extends StringValueObject
{
    /**
     * @var string
     */
    private $countryCode;

    /**
     * @param string $number
     * @param string $iso2CountryCode
     *
     * @throws NumberParseException
     */
    public function __construct(string $number, string $iso2CountryCode)
    {
        Assert::that($iso2CountryCode)->string()->length(2);

        $phoneUtil = $this->getPhoneNumberUtil();

        $phoneNumber = $phoneUtil->parse($number, strtoupper($iso2CountryCode));
        $number = ltrim($phoneUtil->format($phoneNumber, PhoneNumberFormat::E164), '+');

        parent::__construct($number);
        $this->countryCode = $iso2CountryCode;
    }

    /**
     * @return PhoneNumberUtil
     */
    protected function getPhoneNumberUtil(): PhoneNumberUtil
    {
        return PhoneNumberUtil::getInstance();
    }

    /**
     * @return string
     */
    public function getCountryCode() :string
    {
        return $this->countryCode;
    }
}
