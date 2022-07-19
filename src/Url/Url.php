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

namespace D3\LinkmobilityClient\Url;

class Url implements UrlInterface
{
    public $baseUri = 'https://api.linkmobility.eu/rest';

    /**
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    /**
     * @return string
     */
    public function getTextSmsUri(): string
    {
        return '/rest/smsmessaging/text';
    }

    /**
     * @return string
     */
    public function getBinarySmsUri(): string
    {
        return '/rest/smsmessaging/binary';
    }
}
