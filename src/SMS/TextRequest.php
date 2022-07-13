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

namespace D3\LinkmobilityClient\SMS;

use Assert\Assert;
use D3\LinkmobilityClient\Request\Request;
use D3\LinkmobilityClient\ValueObject\SmsTextMessage;
use InvalidArgumentException;

class TextRequest extends Request implements SmsRequestInterface
{
    /**
     * @return string
     */
    public function getUri(): string
    {
        return '/rest/smsmessaging/text';
    }

    /**
     * @throws InvalidArgumentException
     */
    public function validate()
    {
        parent::validate();

        Assert::thatNullOr($this->getMessage())->isInstanceOf(SmsTextMessage::class);
    }

    /**
     * @return string
     */
    public function getResponseClass(): string
    {
        return Response::class;
    }

    public function getRawBody(): array
    {
        return array_merge(
            parent::getRawBody(),
            [
                'maxSmsPerMessage'  => $this->getMaxSmsPerMessage(),
                'messageType'       => $this->getMessageType(),
            ]
        );
    }
}
