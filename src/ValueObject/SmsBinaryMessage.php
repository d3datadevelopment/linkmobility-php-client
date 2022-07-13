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
