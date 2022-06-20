<?php

declare(strict_types=1);

namespace D3\LinkmobilityClient\ValueObject;

use Assert\Assert;

class Recipient extends StringValueObject
{
    public function __construct(string $value)
    {
        // ohne +, dafür mit Ländervorwahl
        // eine führende 0 scheint lokale Version
        // zwei führende Nullen einfach weggeschnitten

        //https://github.com/matmar10/msisdn-format-bundle/blob/master/Matmar10/Bundle/MsisdnFormatBundle/Resources/config/msisdn-country-formats.xml


        // valid formats can be found here: https://linkmobility.atlassian.net/wiki/spaces/COOL/pages/26017807/08.+Messages#id-08.Messages-recipients
        Assert::that($value)->regex('/^(\+|c)?[0-9]+$/i', 'Recipient does not match valid phone number.');

        parent::__construct($value);
    }
}
