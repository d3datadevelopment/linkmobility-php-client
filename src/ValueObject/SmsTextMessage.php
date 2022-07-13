<?php

declare(strict_types=1);

namespace D3\LinkmobilityClient\ValueObject;

use InvalidArgumentException;

class SmsTextMessage extends SmsMessageAbstract
{
    /**
     * @param string $message
     * @throws InvalidArgumentException
     */
    public function __construct(string $message)
    {
        parent::__construct($message);

        $smsLength = $this->getSmsLength();
        $smsLength->validate();
    }
}
