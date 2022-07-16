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

namespace D3\LinkmobilityClient\Tests\Response;

use D3\LinkmobilityClient\Response\Response;
use D3\LinkmobilityClient\Tests\ApiTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use ReflectionException;

abstract class AbstractResponse extends ApiTestCase
{
    protected $testClassName;

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\Response::__construct
     * @covers \D3\LinkmobilityClient\SMS\Response::getRawResponse
     * @covers \D3\LinkmobilityClient\SMS\Response::getContent
     */
    public function testConstruct()
    {
        /** @var StreamInterface|MockObject $streamMock */
        $streamMock = $this->getMockBuilder(StreamInterface::class)
            ->onlyMethods(['getContents', '__toString', 'close', 'detach', 'getSize', 'tell', 'eof', 'isSeekable',
                'seek', 'rewind', 'isWritable', 'write', 'isReadable', 'read', 'getMetadata'])
            ->getMock();
        $streamMock->expects($this->atLeastOnce())->method('getContents')->willReturn(
            '{
                "contentCategory": "informational",
                "messageContent": "fixture",
                "senderAddressType": "international"
            }'
        );

        /** @var ResponseInterface|MockObject $rawResponseMock */
        $rawResponseMock = $this->getMockBuilder(ResponseInterface::class)
            ->onlyMethods([
                'getBody', 'getStatusCode', 'withStatus', 'getReasonphrase', 'getProtocolVersion',
                'withProtocolVersion', 'getHeaders', 'hasHeader', 'getHeader', 'getHeaderLine',
                'withHeader', 'withAddedHeader', 'withoutHeader', 'withBody'])
            ->getMock();
        $rawResponseMock->method('getBody')->willReturn($streamMock);

        /** @var Response $response */
        $response = new $this->testClassName($rawResponseMock);

        $this->assertSame(
            $rawResponseMock,
            $this->callMethod(
                $response,
                'getRawResponse'
            )
        );

        $this->assertSame(
            [
                "contentCategory" => "informational",
                "messageContent" => "fixture",
                "senderAddressType" => "international",
                ],
            $this->callMethod(
                $response,
                'getContent'
            )
        );
    }

    /**
     * @throws ReflectionException
     */
    protected function checkProperties($expected, $propertyName, $methodName)
    {
        /** @var Response $response */
        $responseMock = $this->getMockBuilder($this->testClassName)
            ->disableOriginalConstructor()
            ->onlyMethods(['getContent'])
            ->getMock();
        $responseMock->method('getContent')->willReturn([$propertyName   => $expected]);

        $this->assertSame(
            $expected,
            $this->callMethod(
                $responseMock,
                $methodName
            )
        );
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\Response::getInternalStatus
     */
    public function testGetInternalStatus()
    {
        $this->checkProperties(200, 'statusCode', 'getInternalStatus');
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\Response::getStatusMessage
     */
    public function testGetStatusMessage()
    {
        $this->checkProperties('statusMessage', 'statusMessage', 'getStatusMessage');
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\Response::getClientMessageId
     */
    public function testGetClientMessageId()
    {
        $this->checkProperties('clientMessageId', 'clientMessageId', 'getClientMessageId');
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\Response::getTransferId
     */
    public function testGetTransferId()
    {
        $this->checkProperties('transferId', 'transferId', 'getTransferId');
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\Response::getSmsCount
     */
    public function testGetSmsCount()
    {
        $this->checkProperties(5, 'smsCount', 'getSmsCount');
    }

    /**
     * @test
     * @param $statusCode
     * @param $expected
     *
     * @throws ReflectionException
     * @dataProvider isSuccessfulDataProvider
     * @covers \D3\LinkmobilityClient\SMS\Response::isSuccessful
     */
    public function testIsSuccessful($statusCode, $expected)
    {
        /** @var Response|MockObject $responseMock */
        $responseMock = $this->getMockBuilder($this->testClassName)
            ->disableOriginalConstructor()
            ->onlyMethods(['getInternalStatus'])
            ->getMock();
        $responseMock->method('getInternalStatus')->willReturn($statusCode);

        $this->assertSame(
            $expected,
            $this->callMethod(
                $responseMock,
                'isSuccessful'
            )
        );
    }

    /**
     * @return array[]
     */
    public function isSuccessfulDataProvider(): array
    {
        return [
            'below 2000'    => [1999, false],
            'between 2000'  => [2000, true],
            'above 3000'    => [3000, false],
        ];
    }

    /**
     * @test
     * @param $successful
     * @param $expected
     *
     * @throws ReflectionException
     * @dataProvider getErrorMessageDataProvider
     * @covers \D3\LinkmobilityClient\SMS\Response::getErrorMessage
     */
    public function testGetErrorMessage($successful, $expected)
    {
        /** @var Response|MockObject $responseMock */
        $responseMock = $this->getMockBuilder($this->testClassName)
            ->disableOriginalConstructor()
            ->onlyMethods(['isSuccessful', 'getStatusMessage'])
            ->getMock();
        $responseMock->method('isSuccessful')->willReturn($successful);
        $responseMock->method('getStatusMessage')->willReturn('fixtureMessage');

        $this->assertSame(
            $expected,
            $this->callMethod(
                $responseMock,
                'getErrorMessage'
            )
        );
    }

    /**
     * @return array[]
     */
    public function getErrorMessageDataProvider(): array
    {
        return [
            'successful'    => [true, ''],
            'not successful'=> [false, 'fixtureMessage']
        ];
    }
}
