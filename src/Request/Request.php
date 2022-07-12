<?php

/**
 * This Software is the property of Data Development and is protected
 * by copyright law - it is NOT Freeware.
 * Any unauthorized use of this software without a valid license
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 * http://www.shopmodule.com
 *
 * @copyright (C) D3 Data Development (Inh. Thomas Dartsch)
 * @author        D3 Data Development - Daniel Seifert <support@shopmodule.com>
 * @link          http://www.oxidmodule.com
 */

declare( strict_types = 1 );

namespace D3\LinkmobilityClient\Request;

use Assert\Assert;
use D3\LinkmobilityClient\Client;
use D3\LinkmobilityClient\RecipientsList\RecipientsList;
use D3\LinkmobilityClient\RecipientsList\RecipientsListInterface;
use D3\LinkmobilityClient\Response\ResponseInterface;
use D3\LinkmobilityClient\ValueObject\Recipient;
use D3\LinkmobilityClient\ValueObject\Sender;
use D3\LinkmobilityClient\ValueObject\SmsMessageAbstract;
use D3\LinkmobilityClient\ValueObject\SmsMessageInterface;
use D3\LinkmobilityClient\ValueObject\StringValueObject;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;

abstract class Request implements RequestInterface
{
    /**
     * @var SmsMessageInterface
     */
    private $message;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $method = RequestInterface::METHOD_POST;

    /**
     * @var string
     */
    private $contentType = RequestInterface::CONTENTTYPE_JSON;

    /**
     * @var null|string
     */
    private $clientMessageId;

    /**
     * @var string
     */
    private $contentCategory = RequestInterface::CONTENTCATEGORY_INFORMATIONAL;

    /**
     * @var string
     */
    private $messageType = RequestInterface::MESSAGETYPE_DEFAULT;

    /**
     * @var string|null
     */
    private $notificationCallbackUrl = null;

    /**
     * @var null|int
     */
    private $priority = null;

    /**
     * @var RecipientsListInterface
     */
    private $recipientsList;

    /**
     * @var bool
     */
    private $sendAsFlashSms = false;

    /**
     * @var Sender|null
     */
    private $senderAddress = null;

    /**
     * @var string|null
     */
    private $senderAddressType = null;

    /**
     * @var int|null
     */
    private $validityPeriode = null;

    /**
     * @var bool
     */
    private $test = false;

    /**
     * @var bool
     */
    private $maxSmsPerMessage = 0;

    /**
     * @param SmsMessageAbstract $message
     */
    public function __construct(SmsMessageInterface $message, Client $client)
    {
        $this->recipientsList = new RecipientsList($client);
        $this->setMessage( $message );
        $this->setClient($client);

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function validate()
    {
        Assert::that( $this->getMethod() )->choice( self::getMethods() );
        Assert::that( $this->getUri() )->string()->startsWith( '/' );

        Assert::that($this->getBody())->isArray();
        Assert::that($this->getResponseClass())->implementsInterface(ResponseInterface::class);
        Assert::that($this->getOptions())->isArray();

        Assert::that( $this->getRecipientsList() )->isInstanceOf(RecipientsList::class)->notEmpty();
        Assert::that( $this->getRecipientsList()->getRecipients())->notEmpty('request must contain a valid recipient');
        Assert::thatAll( $this->getRecipientsList() )->isInstanceOf( Recipient::class )->notEmpty();

        // optional properties
        Assert::thatNullOr( $this->getClientMessageId() )->string();
        Assert::thatNullOr( $this->getContentCategory() )->choice(self::getContentCategories());
        Assert::thatNullOr( $this->getNotificationCallbackUrl() )->url();
        Assert::thatNullOr( $this->getPriority() )->integer();
        Assert::thatNullOr( $this->doSendAsFlashSms() )->boolean();
        Assert::thatNullOr( $this->getSenderAddress() )->isInstanceOf(Sender::class);
        Assert::thatNullOr( $this->getSenderAddressType() )->choice(self::getSenderAddressTypes());
        Assert::thatNullOr( $this->getTestMode() )->boolean();
        Assert::thatNullOr( $this->getValidityPeriode() )->integer();
    }

    public function getRawBody() : array
    {
        return [
            'clientMessageId'   => $this->getClientMessageId(),
            'contentCategory'   => $this->getContentCategory(),
            'messageContent'    => $this->getMessage()->getMessageContent(),
            'notificationCallbackUrl'   => $this->getNotificationCallbackUrl(),
            'priority'          => $this->getPriority(),
            'recipientAddressList'  => $this->getRecipientsList()->getRecipients(),
            'sendAsFlashSms'    => $this->doSendAsFlashSms(),
            'senderAddress'     => $this->getSenderAddress() ? $this->getSenderAddress()->getFormatted() : null,
            'senderAddressType' => $this->getSenderAddressType(),
            'test'              => $this->getTestMode(),
            'validityPeriode'   => $this->getValidityPeriode()
        ];
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        $body = array_filter( $this->getRawBody(), function( $elm ) {
            return ! is_null( $elm );
        } );

        switch ($this->getContentType()) {
            case RequestInterface::CONTENTTYPE_JSON:
                return [RequestOptions::JSON  => $body];
            default:
                return $body;
        }
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return array_merge(
            [
                'headers' => [
                    'Accept'            => $this->contentType,
                    'Content-Type'      => $this->contentType,
                ]
            ],
            $this->getBody()
        );
    }

    /**
     * @param StringValueObject $message
     *
     * @return $this
     */
    public function setMessage(StringValueObject $message): Request
    {
        $this->message = $message;

        return $this;
    }

    public function getMessage(): SmsMessageInterface
    {
        return $this->message;
    }

    /**
     * @param string $method
     *
     * @return $this
     */
    public function setMethod(string $method): Request
    {
        $this->method = $method;

        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public static function getMethods(): array
    {
        return [
            RequestInterface::METHOD_GET    => RequestInterface::METHOD_GET,
            RequestInterface::METHOD_POST   => RequestInterface::METHOD_POST,
            RequestInterface::METHOD_PUT    => RequestInterface::METHOD_PUT,
            RequestInterface::METHOD_PATCH  => RequestInterface::METHOD_PATCH,
            RequestInterface::METHOD_DELETE => RequestInterface::METHOD_DELETE,
        ];
    }

    /**
     * @param string $contentType
     *
     * @return $this
     */
    public function setContentType(string $contentType): Request
    {
        $this->contentType = $contentType;

        return $this;
    }

    public function getContentType() : string
    {
        return $this->contentType;
    }

    /**
     * @param $clientMessageId
     *
     * @return $this
     */
    public function setClientMessageId($clientMessageId): Request
    {
        $this->clientMessageId = $clientMessageId;

        return $this;
    }

    public function getClientMessageId()
    {
        return $this->clientMessageId;
    }

    /**
     * @param string $contentCategory
     *
     * @return $this
     */
    public function setContentCategory(string $contentCategory): Request
    {
        $this->contentCategory = $contentCategory;

        return $this;
    }

    public function getContentCategory() : string
    {
        return $this->contentCategory;
    }

    public static function getContentCategories(): array
    {
        return [
            RequestInterface::CONTENTCATEGORY_ADVERTISEMENT    => RequestInterface::CONTENTCATEGORY_ADVERTISEMENT,
            RequestInterface::CONTENTCATEGORY_INFORMATIONAL    => RequestInterface::CONTENTCATEGORY_INFORMATIONAL
        ];
    }

    /**
     * @param bool $test
     *
     * @return $this
     */
    public function setTestMode(bool $test): Request
    {
        $this->test = $test;

        return $this;
    }

    public function getTestMode() : bool
    {
        return $this->test;
    }

    /**
     * @param int $maxSmsPerMessage
     *
     * @return $this
     */
    public function setMaxSmsPerMessage(int $maxSmsPerMessage): Request
    {
        $this->maxSmsPerMessage = $maxSmsPerMessage;

        return $this;
    }

    public function getMaxSmsPerMessage() : int
    {
        return $this->maxSmsPerMessage;
    }

    /**
     * @param string $messageType
     *
     * @return $this
     */
    public function setMessageType(string $messageType): Request
    {
        $this->messageType = $messageType;

        return $this;
    }

    public function getMessageType() : string
    {
        return $this->messageType;
    }

    /**
     * @param string $notificationCallbackUrl
     *
     * @return $this
     */
    public function setNotificationCallbackUrl(string $notificationCallbackUrl): Request
    {
        $this->notificationCallbackUrl = $notificationCallbackUrl;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNotificationCallbackUrl()
    {
        return $this->notificationCallbackUrl;
    }

    /**
     * @param int $priority
     *
     * @return $this
     */
    public function setPriority(int $priority): Request
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @return RecipientsListInterface
     */
    public function getRecipientsList() : RecipientsListInterface
    {
        return $this->recipientsList;
    }

    /**
     * @param bool $flashSms
     *
     * @return $this
     */
    public function sendAsFlashSms(bool $flashSms): Request
    {
        $this->sendAsFlashSms = $flashSms;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function doSendAsFlashSms() : bool
    {
        return $this->sendAsFlashSms;
    }

    /**
     * @param Sender $senderAddress
     *
     * @return $this
     */
    public function setSenderAddress(Sender $senderAddress): Request
    {
        $this->senderAddress = $senderAddress;

        return $this;
    }

    /**
     * @return Sender|null
     */
    public function getSenderAddress()
    {
        return $this->senderAddress;
    }

    /**
     * @param string $senderAddressType
     *
     * @return $this
     */
    public function setSenderAddressType(string $senderAddressType): Request
    {
        $this->senderAddressType = $senderAddressType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSenderAddressType()
    {
        return $this->senderAddressType;
    }

    /**
     * @return array
     */
    public static function getSenderAddressTypes(): array
    {
        return [
            RequestInterface::SENDERADDRESSTYPE_ALPHANUMERIC    => RequestInterface::SENDERADDRESSTYPE_ALPHANUMERIC,
            RequestInterface::SENDERADDRESSTYPE_INTERNATIONAL   => RequestInterface::SENDERADDRESSTYPE_INTERNATIONAL,
            RequestInterface::SENDERADDRESSTYPE_NATIONAL        => RequestInterface::SENDERADDRESSTYPE_NATIONAL,
            RequestInterface::SENDERADDRESSTYPE_SHORTCODE       => RequestInterface::SENDERADDRESSTYPE_SHORTCODE
        ];
    }

    /**
     * @param int $validityPeriode
     *
     * @return $this
     */
    public function setValidityPeriode( int $validityPeriode): Request
    {
        $this->validityPeriode = $validityPeriode;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getValidityPeriode()
    {
        return $this->validityPeriode;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $rawResponse
     *
     * @return ResponseInterface
     */
    public function getResponseInstance(\Psr\Http\Message\ResponseInterface $rawResponse): ResponseInterface
    {
        $FQClassName = $this->getResponseClass();
        return new $FQClassName($rawResponse);
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient( Client $client ): Request
    {
        $this->client = $client;

        return $this;
    }
}