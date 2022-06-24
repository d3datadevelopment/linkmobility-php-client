<?php

declare(strict_types=1);

namespace D3\LinkmobilityClient\ValueObject;

use Assert\Assert;

class Sender extends StringValueObject
{
    public function __construct(string $number, string $iso2CountryCode)
    {
        Assert::that($iso2CountryCode)->string()->length(2);

        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        try {
            $phoneNumber = $phoneUtil->parse( $number, strtoupper($iso2CountryCode) );
            $number      = $phoneUtil->format( $phoneNumber, \libphonenumber\PhoneNumberFormat::E164 );

            if (false === $phoneUtil->isValidNumber($phoneNumber)) {
                throw new \D3\LinkmobilityClient\Exceptions\RecipientException( 'invalid sender phone number' );
            }
        } catch (\libphonenumber\NumberParseException $e) {
            var_dump($e);
        }

        parent::__construct( $number);
    }
}
