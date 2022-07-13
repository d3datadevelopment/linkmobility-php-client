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

namespace D3\LinkmobilityClient\Tests\SMS;

use D3\LinkmobilityClient\Client;
use D3\LinkmobilityClient\SMS\BinaryRequest;
use D3\LinkmobilityClient\SMS\RequestFactory;
use D3\LinkmobilityClient\SMS\TextRequest;
use D3\LinkmobilityClient\Tests\ApiTestCase;
use Phlib\SmsLength\SmsLength;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;

class RequestFactoryTest extends ApiTestCase
{
    /**
     * @test
     * @return void
     * @throws ReflectionException
     */
    public function testConstruct()
    {
        $message = 'fixtureMessage';

        /** @var Client|MockObject $clientMock */
        $clientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestFactory = new RequestFactory($message, $clientMock);

        $this->assertSame(
            $message,
            $this->getValue($requestFactory, 'message')
        );
        $this->assertSame(
            $clientMock,
            $this->getValue($requestFactory, 'client')
        );
    }

    /**
     * @test
     * @param $encoding
     * @param $expectedClass
     * @return void
     * @throws ReflectionException
     * @dataProvider getTextSmsRequestDataProvider
     */
    public function testGetTextSmsRequest($encoding, $expectedClass)
    {
        $message = 'fixtureMessage';

        /** @var Client|MockObject $clientMock */
        $clientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var SmsLength|MockObject $smsLengthMock */
        $smsLengthMock = $this->getMockBuilder(SmsLength::class)
            ->onlyMethods(['getEncoding'])
            ->disableOriginalConstructor()
            ->getMock();
        $smsLengthMock->method('getEncoding')->willReturn($encoding);

        /** @var RequestFactory|MockObject $requestFactoryMock */
        $requestFactoryMock = $this->getMockBuilder(RequestFactory::class)
            ->setConstructorArgs([$message, $clientMock])
            ->onlyMethods(['getSmsLength'])
            ->getMock();
        $requestFactoryMock->method('getSmsLength')->willReturn($smsLengthMock);

        $this->assertInstanceOf(
            $expectedClass,
            $this->callMethod(
                $requestFactoryMock,
                'getSmsRequest'
            )
        );
    }

    /**
     * @return array[]
     */
    public function getTextSmsRequestDataProvider(): array
    {
        return [
            'binary'    => [RequestFactory::GSM_UCS2, BinaryRequest::class],
            'ascii'     => [RequestFactory::GSM_7BIT, TextRequest::class]
        ];
    }

    /**
     * @test
     * @return void
     * @throws ReflectionException
     */
    public function testGetSmsLengthInstance()
    {
        $message = 'fixtureMessage';

        /** @var Client|MockObject $clientMock */
        $clientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var RequestFactory|MockObject $requestFactoryMock */
        $requestFactoryMock = $this->getMockBuilder(RequestFactory::class)
            ->setConstructorArgs([$message, $clientMock])
            ->getMock();

        $this->assertInstanceOf(
            SmsLength::class,
            $this->callMethod(
                $requestFactoryMock,
                'getSmsLength'
            )
        );
    }
}
