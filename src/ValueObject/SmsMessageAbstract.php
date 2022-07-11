<?php

declare(strict_types=1);

namespace D3\LinkmobilityClient\ValueObject;

use Phlib\SmsLength\SmsLength;

abstract class SmsMessageAbstract extends StringValueObject implements SmsMessageInterface
{
    /**
     * @return SmsLength
     */
    public function getSmsLength(): SmsLength
    {
        return new SmsLength($this->get());
    }

    /**
     * @return int
     */
    public function chunkCount() : int
    {
        return $this->getSmsLength()->getMessageCount();
    }

    /**
     * @return int
     */
    public function length() : int
    {
        return $this->getSmsLength()->getSize();
    }

    public function getMessageContent()
    {
        return $this->get();
    }
}
