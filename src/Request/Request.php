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
use D3\LinkmobilityClient\RecipientsList\RecipientsList;
use D3\LinkmobilityClient\RecipientsList\RecipientsListInterface;
use D3\LinkmobilityClient\Response\ResponseInterface;
use D3\LinkmobilityClient\ValueObject\Recipient;
use D3\LinkmobilityClient\ValueObject\Sender;
use D3\LinkmobilityClient\ValueObject\SmsMessage;
use D3\LinkmobilityClient\ValueObject\StringValueObject;
use OxidEsales\Eshop\Core\Registry;

abstract class Request implements RequestInterface
{
    /**
     * @var StringValueObject
     */
    private $message;

    /**
     * @var string
     */
    private $method = RequestInterface::METHOD_GET;

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
     * @var string|null
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

    public function __construct(StringValueObject $message)
    {
        $this->recipientsList = new RecipientsList();
        $this->setMessage( $message );

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function validate(): void
    {
        Assert::that( $this->getMethod() )->choice( self::getMethods() );
        Assert::that( $this->getUri() )->string()->startsWith( '/' );

        Assert::that($this->getBody())->isArray();
        Assert::that($this->getResponseClass())->implementsInterface(ResponseInterface::class);
        Assert::that($this->getOptions())->isArray();

        Assert::that( $this->getRecipientsList() )->isInstanceOf(RecipientsList::class)->notEmpty();
        Assert::thatAll( $this->getRecipientsList() )->isInstanceOf( Recipient::class );

        // optional properties
        Assert::thatNullOr( $this->getClientMessageId() )->string();
        Assert::thatNullOr( $this->getContentCategory() )->choice(self::getContentCategories());
        Assert::thatNullOr( $this->getNotificationCallbackUrl() )->url();
        Assert::thatNullOr( $this->getPriority() )->integer();
        Assert::thatNullOr( $this->getSendAsFlashSms() )->boolean();
        Assert::thatNullOr( $this->getSenderAddress() )->isInstanceOf(Sender::class);
        Assert::thatNullOr( $this->getSenderAddressType() )->choice(self::getSenderAddressTypes());
        Assert::thatNullOr( $this->isTest() )->boolean();
        Assert::thatNullOr( $this->getValidityPeriode() )->integer();
    }

    public function getRawBody() : array
    {
        return [
            'clientMessageId'   => $this->getClientMessageId(),
            'contentCategory'   => $this->getContentCategory(),
            'maxSmsPerMessage'  => $this->getMaxSmsPerMessage(),
            'messageContent'    => (string) $this->getMessage(),
            'messageType'       => $this->getMessageType(),
            'notificationCallbackUrl'   => $this->getNotificationCallbackUrl(),
            'priority'          => $this->getPriority(),
            'recipientAddressList'  => $this->getRecipientsList()->getRecipients(),
            'sendAsFlashSms'    => $this->getSendAsFlashSms(),
            'senderAddress'     => $this->getSenderAddress()->get(),
            'senderAddressType' => $this->getSenderAddressType(),
            'test'              => $this->isTest(),
            'validityPeriode'    => $this->getValidityPeriode()
        ];
    }

    public function getBody(): array
    {
        $body = array_filter( $this->getRawBody(), function( $elm ) {
            return ! is_null( $elm );
        } );

        switch ($this->getContentType()) {
            case RequestInterface::CONTENTTYPE_JSON:
                return ['json'  => json_encode($body)];
            default:
                return $body;
        }
    }

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

    public function setMessage(StringValueObject $message)
    {
        $this->message = $message;

        return $this;
    }

    public function getMessage(): StringValueObject
    {
        return $this->message;
    }

    public function setMethod(string $method)
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

    public function setContentType(string $contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    public function getContentType() : string
    {
        return $this->contentType;
    }

    public function setClientMessageId($clientMessageId)
    {
        $this->clientMessageId = $clientMessageId;

        return $this;
    }

    public function getClientMessageId()
    {
        return $this->clientMessageId;
    }

    public function setContentCategory(string $contentCategory)
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

    public function setTest(bool $test)
    {
        $this->test = $test;

        return $this;
    }

    public function isTest() : bool
    {
        return $this->test;
    }

    public function setMaxSmsPerMessage(int $maxSmsPerMessage)
    {
        $this->maxSmsPerMessage = $maxSmsPerMessage;

        return $this;
    }

    public function getMaxSmsPerMessage() : int
    {
        return $this->maxSmsPerMessage;
    }

    public function setMessageType(string $messageType)
    {
        $this->messageType = $messageType;

        return $this;
    }

    public function getMessageType() : string
    {
        return $this->messageType;
    }

    /**
     * @param string|null $notificationCallbackUrl
     *
     * @return $this
     */
    public function setNotificationCallbackUrl($notificationCallbackUrl)
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
     * @param string|null $priority
     *
     * @return $this
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return string|null
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
     * @param array $recipientList
     *
     * @return $this
     */
    public function setSendAsFlashSms(bool $flashSms)
    {
        $this->sendAsFlashSms = $flashSms;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSendAsFlashSms() : bool
    {
        return $this->sendAsFlashSms;
    }

    /**
     * @param Sender $senderAddress
     *
     * @return $this
     */
    public function setSenderAddress(Sender $senderAddress)
    {
        $this->senderAddress = $senderAddress;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSenderAddress() : Sender
    {
        return $this->senderAddress;
    }

    /**
     * @param $senderAddressType
     *
     * @return $this
     */
    public function setSenderAddressType($senderAddressType)
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

    public static function getSenderAddressTypes(): array
    {
        return [
            RequestInterface::SENDERADDRESSTYPE_ALPHANUMERIC    => RequestInterface::SENDERADDRESSTYPE_ALPHANUMERIC,
            RequestInterface::SENDERADDRESSTYPE_INTERNATIONAL    => RequestInterface::SENDERADDRESSTYPE_INTERNATIONAL,
            RequestInterface::SENDERADDRESSTYPE_NATIONAL    => RequestInterface::SENDERADDRESSTYPE_NATIONAL,
            RequestInterface::SENDERADDRESSTYPE_SHORTCODE    => RequestInterface::SENDERADDRESSTYPE_SHORTCODE
        ];
    }

    /**
     * @param $validityPeriode
     *
     * @return $this
     */
    public function setValidityPeriode($validityPeriode)
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
     * @return ResponseInterface
     */
    public function getResponseInstance(\Psr\Http\Message\ResponseInterface $rawResponse): ResponseInterface
    {
        return new $this->getResponseClass($rawResponse);
    }
}