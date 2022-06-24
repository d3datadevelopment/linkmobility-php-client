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

namespace D3\LinkmobilityClient\Exceptions;

class ExceptionMessages
{
    const INVALID_SENDER            = 'invalid sender phone number';

    const WRONG_APIURL_INTERFACE    = 'ApiUrl instance must implement UrlInterface';

    const NOK_REQUEST_RETURN        = 'request %1$s returns status code %2$s';

    const INVALID_RECIPIENT_PHONE   = 'invalid recipient phone number';

    const NOT_A_MOBILE_NUMBER       = 'not a mobile number';
}