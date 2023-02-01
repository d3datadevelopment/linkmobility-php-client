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

namespace D3\LinkmobilityClient\ValueObject;

use Assert\InvalidArgumentException;

class SmsTextMessage extends SmsMessageAbstract
{
    /**
     * @param string $message
     * @throws InvalidArgumentException
     */
    public function __construct(string $message)
    {
        parent::__construct($message);

        $smsLength = $this->getSmsLength();
        $smsLength->validate();
    }
}
