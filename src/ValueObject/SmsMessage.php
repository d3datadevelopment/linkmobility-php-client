<?php

declare(strict_types=1);

namespace D3\LinkmobilityClient\ValueObject;

use Assert\Assert;
use Phlib\SmsLength\SmsLength;

class SmsMessage extends StringValueObject
{
    const GSM_7BIT = '7-bit';
    const GSM_UCS2 = 'ucs-2';

    public function __construct(string $number)
    {
        parent::__construct( $number);

        $smsLength = new SmsLength($this->value);
        $smsLength->validate();
    }

    /**
     * @var bool
     */
    private $gsm7;

    public function chunkCount() : int
    {
        $smsLength = new SmsLength($this->value);
        return $smsLength->getMessageCount();
    }

    public function length() : int
    {
        $smsLength = new SmsLength($this->value);
        $smsLength->getSize();
    }

    public function isGsm7() : bool
    {
        $smsLength = new SmsLength($this->value);
        if (is_null($this->gsm7)) {
            $this->gsm7 = $smsLength->getEncoding() === self::GSM_7BIT;
        }

        return $this->gsm7;
    }

    public function isUnicode() : bool
    {
        return !$this->isGsm7();
    }
}
