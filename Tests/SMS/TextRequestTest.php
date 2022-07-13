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

namespace D3\LinkmobilityClient\Tests\SMS;

use D3\LinkmobilityClient\SMS\TextRequest;
use D3\LinkmobilityClient\Tests\Request\AbstractRequest;
use D3\LinkmobilityClient\ValueObject\SmsTextMessage;

class TextRequestTest extends AbstractRequest
{
    protected $testClassName = TextRequest::class;
    protected $messageClassName = SmsTextMessage::class;
}
