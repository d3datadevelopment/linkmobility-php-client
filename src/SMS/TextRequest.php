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

namespace D3\LinkmobilityClient\SMS;

use Assert\Assert;
use D3\LinkmobilityClient\Request\RequestInterface;
use D3\LinkmobilityClient\Url;
use D3\LinkmobilityClient\ValueObject\Message;
use D3\LinkmobilityClient\ValueObject\Sender;
use D3\LinkmobilityClient\ValueObject\SmsMessage;

class TextRequest extends \D3\LinkmobilityClient\Request\Request
{
    /**
     * @var Sender
     */
    protected $sender;

    /**
     * @var boolean
     */
    protected $status;

    /**
     * @var string
     */
    protected $statusUrl;

    /**
     * @var string
     */
    protected $returnData;

    /**
     * @var int
     */
    protected $class;

    /**
     * @var \DateTimeInterface
     */
    protected $sendTime;

    /**
     * @var int
     */
    protected $price;

    /**
     * @var boolean
     */
    protected $charity;

    /**
     * @var string
     */
    protected $invoiceText;

    /**
     * @var int
     */
    protected $validity;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var string
     */
    protected $udh;

    /**
     * @var array
     */
    protected $attachment;

    /**
     * @var string
     */
    protected $pushUrl;

    /**
     * @var string
     */
    protected $pushExpire;

    /**
     * @var array
     */
    protected $filter;

    /**
     * @var array
     */
    protected $segmentation;

    /**
     * @var int
     */
    protected $pid;

    /**
     * @var string
     */
    protected $advanced;

    /**
     * @var string
     */
    protected $protocol;

    /**
     * @var string
     */
    protected $revenueText;

    public function getUri(): string
    {
        return '/smsmessaging/text/';
    }

    public function validate(): void
    {
        parent::validate();

        Assert::thatNullOr( $this->getMessage() )->isInstanceOf(SmsMessage::class);
    }

    public function getResponseClass(): string
    {
        return Response::class;
    }

    /**
     * @param Message $message
     *
     * @return PostMessageRequest
     */
/*
    public function setMessage( SmsMessage $message )
    {
        $this->message = $message;

        if ($message->isGsm7()) {
            $this->setFormat(SmsMessage::GSM_7BIT);
        } else {
            $this->setFormat(SmsMessage::GSM_UCS2);
        }

        return $this;
    }
*/

    /**
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     *
     * @return PostMessageRequest
     */
    public function setStatus( bool $status )
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getReturnData(): string
    {
        return $this->returnData;
    }

    /**
     * @param string $returnData
     *
     * @return PostMessageRequest
     */
    public function setReturnData( string $returnData )
    {
        $this->returnData = $returnData;

        return $this;
    }

    /**
     * @return int
     */
    public function getClass(): int
    {
        return $this->class;
    }

    /**
     * @param int $class
     *
     * @return PostMessageRequest
     */
    public function setClass( int $class )
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getSendTime(): \DateTimeInterface
    {
        return $this->sendTime;
    }

    /**
     * @param \DateTimeInterface $sendTime
     *
     * @return PostMessageRequest
     */
    public function setSendTime( \DateTimeInterface $sendTime )
    {
        $this->sendTime = $sendTime;

        return $this;
    }

    /**
     * @return int
     */
    public function getValidity(): int
    {
        return $this->validity;
    }

    /**
     * @param int|\DateInterval $validity In minutes
     *
     * @return PostMessageRequest
     */
    public function setValidity( $validity )
    {
        if ( $validity instanceof \DateInterval ) {
            $now      = new \DateTimeImmutable();
            $seconds  = $now->add( $validity )->getTimestamp() - $now->getTimestamp();
            $validity = ceil( $seconds / 60 );
        }

        $validity = (int) $validity;

        $this->validity = $validity;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     *
     * @return PostMessageRequest
     */
    public function setFormat( string $format )
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return string
     */
    public function getUdh(): string
    {
        return $this->udh;
    }

    /**
     * @param string $udh
     *
     * @return PostMessageRequest
     */
    public function setUdh( string $udh )
    {
        $this->udh = $udh;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttachment(): array
    {
        return $this->attachment;
    }

    /**
     * @param array $attachment
     *
     * @return PostMessageRequest
     */
    public function setAttachment( array $attachment )
    {
        $this->attachment = $attachment;

        return $this;
    }

    /**
     * @return string
     */
    public function getPushUrl(): string
    {
        return $this->pushUrl;
    }

    /**
     * @param string $pushUrl
     *
     * @return PostMessageRequest
     */
    public function setPushUrl( string $pushUrl )
    {
        $this->pushUrl = $pushUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getPushExpire(): string
    {
        return $this->pushExpire;
    }

    /**
     * @param string|\DateTimeInterface $pushExpire
     *
     * @return PostMessageRequest
     */
    public function setPushExpire( $pushExpire )
    {
        if ( $pushExpire instanceof \DateTimeInterface ) {
            $pushExpire = (string) $pushExpire->getTimestamp();
        }

        $this->pushExpire = $pushExpire;

        return $this;
    }

    /**
     * @return array
     */
    public function getFilter(): array
    {
        return $this->filter;
    }

    /**
     * @param array $filter
     *
     * @return PostMessageRequest
     */
    public function setFilter( array $filter )
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @return array
     */
    public function getSegmentation(): array
    {
        return $this->segmentation;
    }

    /**
     * @param array $segmentation
     *
     * @return PostMessageRequest
     */
    public function setSegmentation( array $segmentation )
    {
        $this->segmentation = $segmentation;

        return $this;
    }

    /**
     * @return int
     */
    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * @param int $pid
     *
     * @return PostMessageRequest
     */
    public function setPid( int $pid )
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdvanced(): string
    {
        return $this->advanced;
    }

    /**
     * @param string $advanced
     *
     * @return PostMessageRequest
     */
    public function setAdvanced( string $advanced )
    {
        $this->advanced = $advanced;

        return $this;
    }

    /**
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->protocol;
    }

    /**
     * @param string $protocol
     *
     * @return PostMessageRequest
     */
    public function setProtocol( string $protocol )
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * @return string
     */
    public function getRevenueText(): string
    {
        return $this->revenueText;
    }

    /**
     * @param string $revenueText
     *
     * @return PostMessageRequest
     */
    public function setRevenueText( string $revenueText )
    {
        $this->revenueText = $revenueText;

        return $this;
    }
}