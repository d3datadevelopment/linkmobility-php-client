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

abstract class SmsMessageAbstract extends StringValueObject implements SmsMessageInterface
{
    /**
     * @return SmsLength
     */
    public function getSmsLength(): SmsLength
    {
        return new SmsLength($this->get());
    }

    /**
     * @return int
     */
    public function chunkCount(): int
    {
        return $this->getSmsLength()->getMessageCount();
    }

    /**
     * @return int
     */
    public function length(): int
    {
        return $this->getSmsLength()->getSize();
    }

    /**
     * @return mixed
     */
    public function getMessageContent()
    {
        return $this->get();
    }
}
