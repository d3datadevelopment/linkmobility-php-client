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

namespace D3\LinkmobilityClient\Response;

interface ResponseInterface
{
    public function __construct(\Psr\Http\Message\ResponseInterface $rawResponse);

    public function getRawResponse(): \Psr\Http\Message\ResponseInterface;

    public function getInternalStatus(): int;

    public function getStatusMessage(): string;

    public function getClientMessageId();

    public function getTransferId();

    public function getSmsCount(): int;

    public function isSuccessful(): bool;

    public function getErrorMessage(): string;
}
