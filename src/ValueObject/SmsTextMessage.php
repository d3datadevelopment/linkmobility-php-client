<?php

declare(strict_types=1);

namespace D3\LinkmobilityClient\ValueObject;

use InvalidArgumentException;
use Phlib\SmsLength\SmsLength;

class SmsTextMessage extends SmsMessageAbstract
{
    /**
     * @param string $message
     * @throws InvalidArgumentException
     */
    public function __construct(string $message)
    {
        parent::__construct( $message);

        $smsLength = new SmsLength($this->value);
        $smsLength->validate();
    }
}
