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

use Assert\Assert;

abstract class ValueObject
{
    protected $value;

    public function __construct(string $number)
    {
        Assert::that($number)->notEmpty();

        $this->value = $number;
    }

    public function get()
    {
        return $this->value;
    }

    public function getFormatted()
    {
        return $this->get();
    }
}
