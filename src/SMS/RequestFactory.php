<?php

/**
 * This Software is the property of Data Development and is protected
 * by copyright law - it is NOT Freeware.
 * Any unauthorized use of this software without a valid license
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 * http://www.shopmodule.com
 *
 * @copyright (C) D3 Data Development (Inh. Thomas Dartsch)
 * @author        D3 Data Development - Daniel Seifert <support@shopmodule.com>
 * @link          http://www.oxidmodule.com
 */

declare( strict_types = 1 );

namespace D3\LinkmobilityClient\SMS;

use D3\LinkmobilityClient\Client;
use D3\LinkmobilityClient\ValueObject\SmsBinaryMessage;
use D3\LinkmobilityClient\ValueObject\SmsMessageInterface;
use D3\LinkmobilityClient\ValueObject\SmsTextMessage;
use Phlib\SmsLength\SmsLength;

class RequestFactory
{
    /**
     * @deprecated is SmsLength constant from version 2.1
     */
    const GSM_7BIT = '7-bit';

    /**
     * @deprecated is SmsLength constant from version 2.1
     */
    const GSM_UCS2 = 'ucs-2';

    protected $message;
    protected $client;

    public function __construct($message, Client $client)
    {
        $this->message = $message;
        $this->client = $client;
    }

    /**
     * @return SmsRequestInterface
     */
    public function getSmsRequest() : SmsRequestInterface
    {
        $smsLength = new SmsLength($this->message);
        if ($smsLength->getEncoding() === self::GSM_7BIT) {
            $message = new SmsTextMessage($this->message);
            return new TextRequest($message, $this->client);
        }

        $message = new SmsBinaryMessage($this->message);
        return new BinaryRequest($message, $this->client);
    }
}