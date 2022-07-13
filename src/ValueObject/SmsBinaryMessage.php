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
        parent::__construct($message);

        $this->getSmsLength()->validate();
    }

    /**
     * @return array|false
     */
    public function getMessageContent()
    {
        return str_split(
            base64_encode($this->get()),
            SmsLength::MAXIMUM_CHARACTERS_UCS2_SINGLE
        );
    }
}
