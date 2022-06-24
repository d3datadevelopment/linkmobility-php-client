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

use Assert\Assert;
use D3\LinkmobilityClient\ValueObject\SmsMessage;

class TextRequest extends \D3\LinkmobilityClient\Request\Request
{
    /**
     * @return string
     */
    public function getUri(): string
    {
        return '/rest/smsmessaging/text';
    }

    public function validate()
    {
        parent::validate();

        Assert::thatNullOr( $this->getMessage() )->isInstanceOf(SmsMessage::class);
    }

    /**
     * @return string
     */
    public function getResponseClass(): string
    {
        return Response::class;
    }
}