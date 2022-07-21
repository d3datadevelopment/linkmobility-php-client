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

abstract class SmsMessageAbstractTest extends ApiTestCase
{
    public $message;

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        unset($this->message);
    }

    /**
     * @return array[]
     */
    public function constructInvalidDataProvider(): array
    {
        return [
            'empty message'          => ['', true, InvalidArgumentException::class],
            'invalid sms message'    => ['abc', false, \Phlib\SmsLength\Exception\InvalidArgumentException::class],
        ];
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\ValueObject\SmsBinaryMessage::getSmsLength
     * @covers \D3\LinkmobilityClient\ValueObject\SmsTextMessage::getSmsLength
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
}
