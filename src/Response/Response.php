<?php 

declare(strict_types=1);

namespace D3\LinkmobilityClient\Response;

abstract class Response implements ResponseInterface
{
    const STATUSCODE        = 'statusCode';
    const STATUSMESSAGE     = 'statusMessage';
    const CLIENTMESSAGEID   = 'clientMessageId';
    const TRANSFERID        = 'transferId';
    const SMSCOUNT          = 'smsCount';

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
        $this->content = json_decode($this->rawResponse->getBody()->getContents(), true);
    }
    
    public function getRawResponse() : \Psr\Http\Message\ResponseInterface
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
    public function getInternalStatus() : int
    {
        return $this->getContent()[self::STATUSCODE];
    }

    /**
     * @return string
     */
    public function getStatusMessage() : string
    {
        return $this->getContent()[self::STATUSMESSAGE];
    }

    /**
     * @return string|null
     */
    public function getClientMessageId()
    {
        return $this->getContent()[self::CLIENTMESSAGEID];
    }

    /**
     * @return string|null
     */
    public function getTransferId()
    {
        return $this->getContent()[self::TRANSFERID];
    }

    /**
     * @return string|null
     */
    public function getSmsCount() : int
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
