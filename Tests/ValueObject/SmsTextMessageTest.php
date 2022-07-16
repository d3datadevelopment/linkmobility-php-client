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

class SmsTextMessageTest extends SmsBinaryMessageTest
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
}
