<?php

declare(strict_types=1);

namespace D3\LinkmobilityClient\ValueObject;

use Phlib\SmsLength\SmsLength;

class SmsBinaryMessage extends SmsMessageAbstract
{
    /**
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct( $message);

        $smsLength = new SmsLength($this->value);
        $smsLength->validate();
    }

    public function getMessageContent()
    {
        return str_split(
            base64_encode($this->value),
            SmsLength::MAXIMUM_CHARACTERS_UCS2_SINGLE
        );
    }
}
