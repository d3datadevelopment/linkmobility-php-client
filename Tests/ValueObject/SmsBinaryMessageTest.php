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

use Assert\InvalidArgumentException;
use D3\LinkmobilityClient\Tests\ApiTestCase;
use D3\LinkmobilityClient\ValueObject\SmsBinaryMessage;
use Phlib\SmsLength\SmsLength;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;

class SmsBinaryMessageTest extends ApiTestCase
{
    /** @var SmsBinaryMessage */
    public $message;

    private $messageFixture = "testMessage";

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->message = new SmsBinaryMessage($this->messageFixture);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        unset($this->message);
    }

    /**
     * @test
     * @return void
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\ValueObject\SmsBinaryMessage::__construct
     */
    public function testConstructValid()
    {
        /** @var SmsLength|MockObject $smsLengthMock */
        $smsLengthMock = $this->getMockBuilder(SmsLength::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['validate'])
            ->getMock();
        $smsLengthMock->expects($this->atLeastOnce())->method('validate')->willReturn(true);

        /** @var SmsBinaryMessage|MockObject $message */
        $message = $this->getMockBuilder(SmsBinaryMessage::class)
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
     * @covers \D3\LinkmobilityClient\ValueObject\SmsBinaryMessage::__construct
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
            $smsLengthMock->expects($this->atLeastOnce())->method('validate')->willThrowException(new \Phlib\SmsLength\Exception\InvalidArgumentException());
        }

        /** @var SmsBinaryMessage|MockObject $message */
        $message = $this->getMockBuilder(SmsBinaryMessage::class)
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
     * @return string[][]
     */
    public function constructInvalidDataProvider(): array
    {
        return [
            'empty message'          => ['', true, InvalidArgumentException::class],
            'invalid sms message'    => ['abc', false, \Phlib\SmsLength\Exception\InvalidArgumentException::class]
        ];
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\ValueObject\SmsBinaryMessage::getSmsLength
     */
    public function testGetSmsLengthInstance()
    {
        $this->assertInstanceOf(
            SmsLength::class,
            $this->callMethod(
                $this->message,
                'getSmsLength'
            )
        );
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\ValueObject\SmsBinaryMessage::chunkCount
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

        /** @var SmsBinaryMessage|MockObject $message */
        $message = $this->getMockBuilder(SmsBinaryMessage::class)
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
     * @covers \D3\LinkmobilityClient\ValueObject\SmsBinaryMessage::length
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

        /** @var SmsBinaryMessage|MockObject $message */
        $message = $this->getMockBuilder(SmsBinaryMessage::class)
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
     * @covers \D3\LinkmobilityClient\ValueObject\SmsBinaryMessage::getMessageContent
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

        /** @var SmsBinaryMessage|MockObject $message */
        $message = $this->getMockBuilder(SmsBinaryMessage::class)
            ->onlyMethods(['getSmsLength'])
            ->disableOriginalConstructor()
            ->getMock();
        $message->method('getSmsLength')->willReturn($smsLengthMock);
        $message->__construct($this->messageFixture);

        $this->assertSame(
            ['dGVzdE1lc3NhZ2U='],   // binary content
            $this->callMethod(
                $message,
                'getMessageContent'
            )
        );
    }
}
