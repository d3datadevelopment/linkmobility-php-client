<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * https://www.d3data.de
 *
 * @copyright (C) D3 Data Development (Inh. Thomas Dartsch)
 * @author    D3 Data Development - Daniel Seifert <support@shopmodule.com>
 * @link      https://www.oxidmodule.com
 */

declare(strict_types=1);

namespace D3\LinkmobilityClient\Exceptions;

class ExceptionMessages
{
    public const INVALID_SENDER             = 'invalid sender phone number';

    public const NOK_REQUEST_RETURN         = 'request %1$s returns status code %2$s';

    public const INVALID_RECIPIENT_PHONE    = 'invalid recipient phone number';

    public const NOT_A_MOBILE_NUMBER        = 'not a mobile number';

    public const EMPTY_RECIPIENT_LIST       = 'request must contain a valid recipient';

    public const DEBUG_NOSENDERORCOUNTRYCODE= 'no sender number or sender country code defined, use fallback to account default';
}
