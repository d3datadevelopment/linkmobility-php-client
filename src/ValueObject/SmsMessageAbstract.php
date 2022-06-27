<?php

declare(strict_types=1);

namespace D3\LinkmobilityClient\ValueObject;

use Phlib\SmsLength\SmsLength;

abstract class SmsMessageAbstract extends StringValueObject implements SmsMessageInterface
{
    /**
     * @return int
     */
    public function chunkCount() : int
    {
        $smsLength = new SmsLength($this->value);
        return $smsLength->getMessageCount();
    }

    /**
     * @return int
     */
    public function length() : int
    {
        $smsLength = new SmsLength($this->value);
        return $smsLength->getSize();
    }

    public function getMessageContent()
    {
        return (string) $this->value;
    }
}
