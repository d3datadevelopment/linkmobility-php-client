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

namespace D3\LinkmobilityClient\Tests\ValueObject;

use D3\LinkmobilityClient\ValueObject\SmsTextMessage;
use Phlib\SmsLength\Exception\InvalidArgumentException;
use Phlib\SmsLength\SmsLength;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;

class SmsTextMessageTest extends SmsMessageAbstractTest
{
    /** @var SmsTextMessage */
    public $message;

    private $messageFixture = "testMessage";

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->message = new SmsTextMessage($this->messageFixture);
    }

    /**
     * @test
     * @return void
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\ValueObject\SmsTextMessage::__construct
     * @covers \D3\LinkmobilityClient\ValueObject\SmsMessageAbstract::__construct
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
     * @covers \D3\LinkmobilityClient\ValueObject\SmsTextMessage::__construct
     * @covers \D3\LinkmobilityClient\ValueObject\SmsMessageAbstract::__construct
     */
    public function testConstructInvalid($binaryMessage, $valid, $expectedException)
    {
        /** @var SmsLength|MockObject $smsLengthMock */
        $smsLengthMock = $this->getMockBuilder(SmsLength::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['validate'])
            ->getMock();
        if ($valid) {
            $smsLengthMock->expects($this->never())->method('validate')->willReturn(true);
        } else {
            $smsLengthMock->expects($this->atLeastOnce())->method('validate')->willThrowException(new InvalidArgumentException());
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

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\ValueObject\SmsTextMessage::chunkCount
     * @covers \D3\LinkmobilityClient\ValueObject\SmsMessageAbstract::chunkCount
     */
    public function testGetChunkCount()
    {
        $expected = 2;

        /** @var SmsLength|MockObject $smsLengthMock */
        $smsLengthMock = $this->getMockBuilder(SmsLength::class)
            ->onlyMethods(['getMessageCount', 'validate'])
            ->disableOriginalConstructor()
            ->getMock();
        $smsLengthMock->expects($this->once())->method('getMessageCount')->willReturn($expected);
        $smsLengthMock->method('validate')->willReturn(true);

        /** @var SmsTextMessage|MockObject $message */
        $message = $this->getMockBuilder(SmsTextMessage::class)
            ->onlyMethods(['getSmsLength'])
            ->disableOriginalConstructor()
            ->getMock();
        $message->method('getSmsLength')->willReturn($smsLengthMock);

        $this->assertSame(
            $expected,
            $this->callMethod(
                $message,
                'chunkCount'
            )
        );
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\ValueObject\SmsTextMessage::length
     * @covers \D3\LinkmobilityClient\ValueObject\SmsMessageAbstract::length
     */
    public function testGetSize()
    {
        $expected = 55;

        /** @var SmsLength|MockObject $smsLengthMock */
        $smsLengthMock = $this->getMockBuilder(SmsLength::class)
            ->onlyMethods(['getSize', 'validate'])
            ->disableOriginalConstructor()
            ->getMock();
        $smsLengthMock->expects($this->once())->method('getSize')->willReturn($expected);
        $smsLengthMock->method('validate')->willReturn(true);

        /** @var SmsTextMessage|MockObject $message */
        $message = $this->getMockBuilder(SmsTextMessage::class)
            ->onlyMethods(['getSmsLength'])
            ->disableOriginalConstructor()
            ->getMock();
        $message->method('getSmsLength')->willReturn($smsLengthMock);

        $this->assertSame(
            $expected,
            $this->callMethod(
                $message,
                'length'
            )
        );
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\ValueObject\SmsTextMessage::getMessageContent
     * @covers \D3\LinkmobilityClient\ValueObject\SmsMessageAbstract::getMessageContent
     */
    public function testGetMessageContent()
    {
        /** @var SmsLength|MockObject $smsLengthMock */
        $smsLengthMock = $this->getMockBuilder(SmsLength::class)
            ->onlyMethods(['validate'])
            ->disableOriginalConstructor()
            ->getMock();
        $smsLengthMock->method('validate')->willReturn(true);

        /** @var SmsTextMessage|MockObject $message */
        $message = $this->getMockBuilder(SmsTextMessage::class)
            ->onlyMethods(['getSmsLength'])
            ->disableOriginalConstructor()
            ->getMock();
        $message->method('getSmsLength')->willReturn($smsLengthMock);
        $message->__construct($this->messageFixture);

        $this->assertSame(
            'testMessage',
            $this->callMethod(
                $message,
                'getMessageContent'
            )
        );
    }
}
