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

namespace D3\LinkmobilityClient\Tests\ValueObject;

use D3\LinkmobilityClient\ValueObject\SmsTextMessage;
use Phlib\SmsLength\Exception\InvalidArgumentException;
use Phlib\SmsLength\SmsLength;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;

class SmsTextMessageTest extends SmsBinaryMessageTest
{
    /** @var SmsTextMessage */
    public $message;

    private $messageFixture = "testMessage";

    /**
     * @return void
     */
    public function setUp():void
    {
        parent::setUp();

        $this->message = new SmsTextMessage( $this->messageFixture);
    }

    /**
     * @test
     * @return void
     * @throws ReflectionException
     */
    public function testConstructValid()
    {
        /** @var SmsLength|MockObject $smsLengthMock */
        $smsLengthMock = $this->getMockBuilder(SmsLength::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['validate'])
            ->getMock();
        $smsLengthMock->expects($this->atLeastOnce())->method('validate')->willReturn(true);

        /** @var SmsTextMessage|MockObject $message */
        $message = $this->getMockBuilder(SmsTextMessage::class)
            ->onlyMethods(['getSmsLength'])
            ->disableOriginalConstructor()
            ->getMock();
        $message->method('getSmsLength')->willReturn($smsLengthMock);
        $message->__construct($this->messageFixture);

        $this->assertSame(
            $this->messageFixture,
            $this->callMethod(
                $message,
                'get'
            )
        );
    }

    /**
     * @test
     *
     * @param $binaryMessage
     * @param $valid
     * @param $expectedException
     *
     * @throws ReflectionException
     * @dataProvider constructInvalidDataProvider
     */
    public function testConstructInvalid($binaryMessage, $valid, $expectedException)
    {
        /** @var SmsLength|MockObject $smsLengthMock */
        $smsLengthMock = $this->getMockBuilder(SmsLength::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['validate'])
            ->getMock();
        if ($valid) {
            $smsLengthMock->expects( $this->never() )->method( 'validate' )->willReturn( true );
        } else {
            $smsLengthMock->expects( $this->atLeastOnce() )->method( 'validate' )->willThrowException(new InvalidArgumentException());
        }

        /** @var SmsTextMessage|MockObject $message */
        $message = $this->getMockBuilder(SmsTextMessage::class)
            ->onlyMethods(['getSmsLength'])
            ->disableOriginalConstructor()
            ->getMock();
        $message->method('getSmsLength')->willReturn($smsLengthMock);

        $this->expectException($expectedException);
        $message->__construct($binaryMessage);

        $this->assertSame(
            $message,
            $this->callMethod(
                $message,
                'get'
            )
        );
    }
}