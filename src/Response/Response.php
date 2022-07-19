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

abstract class Response implements ResponseInterface
{
    public const STATUSCODE        = 'statusCode';
    public const STATUSMESSAGE     = 'statusMessage';
    public const CLIENTMESSAGEID   = 'clientMessageId';
    public const TRANSFERID        = 'transferId';
    public const SMSCOUNT          = 'smsCount';

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $rawResponse;

    protected $content;

    /**
     * @var int
     */
    protected $status;

    public function __construct(\Psr\Http\Message\ResponseInterface $rawResponse)
    {
        $this->rawResponse = $rawResponse;

        $this->rawResponse->getBody()->rewind();
        $this->content = json_decode($this->rawResponse->getBody()->getContents(), true);
    }

    public function getRawResponse(): \Psr\Http\Message\ResponseInterface
    {
        return $this->rawResponse;
    }

    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getInternalStatus(): int
    {
        return $this->getContent()[self::STATUSCODE];
    }

    /**
     * @return string
     */
    public function getStatusMessage(): string
    {
        return $this->getContent()[self::STATUSMESSAGE];
    }

    /**
     * @return string|null
     */
    public function getClientMessageId(): ?string
    {
        return $this->getContent()[self::CLIENTMESSAGEID];
    }

    /**
     * @return string|null
     */
    public function getTransferId(): ?string
    {
        return $this->getContent()[self::TRANSFERID];
    }

    /**
     * @return int
     */
    public function getSmsCount(): int
    {
        return $this->getContent()[self::SMSCOUNT];
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        $status = $this->getInternalStatus();

        return $status >= 2000 && $status <= 2999;
    }

    public function getErrorMessage(): string
    {
        return $this->isSuccessful() ? '' : $this->getStatusMessage();
    }
}
