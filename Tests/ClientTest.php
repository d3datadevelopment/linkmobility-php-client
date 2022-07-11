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

namespace D3\LinkmobilityClient\Tests;

use Assert\InvalidArgumentException;
use D3\LinkmobilityClient\Client;
use D3\LinkmobilityClient\Exceptions\ApiException;
use D3\LinkmobilityClient\Request\RequestInterface;
use D3\LinkmobilityClient\Response\Response;
use D3\LinkmobilityClient\Response\ResponseInterface;
use D3\LinkmobilityClient\SMS\TextRequest;
use D3\LinkmobilityClient\Url;
use D3\LinkmobilityClient\UrlInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface as MessageResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use ReflectionException;

class ClientTest extends ApiTestCase
{
    public $fixtureApiKey = 'fixtureApiKey';
    /** @var Client */
    public $api;

    /**
     * @return void
     */
    public function setUp():void
    {
        parent::setUp();

        $this->api = new Client($this->fixtureApiKey);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        unset($this->api);
    }

    /**
     * @test
     * @dataProvider constructPassedArgumentsDataProvider
     * @param $apiKey
     * @param $apiUrl
     * @param $apiClient
     * @return void
     * @throws ReflectionException
     */
    public function testConstruct($apiKey, $apiUrl, $apiClient)
    {
        $api =    new Client($apiKey, $apiUrl, $apiClient);

        $this->assertSame(
            $this->getValue($api, 'accessToken'),
            $apiKey
        );
        $this->assertInstanceOf(
            UrlInterface::class,
            $this->getValue($api, 'apiUrl')
        );
        $this->assertInstanceOf(
            ClientInterface::class,
            $this->getValue($api, 'requestClient')
        );
    }

    /**
     * @return array[]
     */
    public function constructPassedArgumentsDataProvider(): array
    {
        return [
            'api key only'  => ['apiKey', null, null],
            'all without client'  => ['apiKey', new Url(), null],
            'all arguments'  => ['apiKey', new Url(), new GuzzleClient()]
        ];
    }

    /**
     * @test
     * @return void
     * @throws ReflectionException
     * @dataProvider requestPassedDataProvider
     *
     */
    public function testRequest($requestIsValid)
    {
        /** @var Client|MockObject apiMock */
        $apiMock = $this->getMockBuilder(Client::class)
            ->onlyMethods(['rawRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $apiMock->expects($this->exactly((int) $requestIsValid))->method('rawRequest');

        /** @var ResponseInterface|MockObject $responseMock */
        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var RequestInterface|MockObject $requestMock */
        $requestMock = $this->getMockBuilder(TextRequest::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['validate', 'getResponseInstance', 'getUri', 'getMethod', 'getOptions'])
            ->getMock();

        /** @var InvalidArgumentException|MockObject $invalidArgExceptionMock */
        $invalidArgExceptionMock = $this->getMockBuilder(InvalidArgumentException::class)
            ->disableOriginalConstructor()
            ->getMock();

        if ($requestIsValid) {
            $requestMock->expects($this->atLeastOnce())->method('validate')->willReturn(true);
        } else {
            $requestMock->expects($this->atLeastOnce())->method('validate')
                ->willThrowException($invalidArgExceptionMock);
        }
        $requestMock->expects($this->exactly((int) $requestIsValid))
            ->method('getResponseInstance')->willReturn($responseMock);
        $requestMock->expects($this->exactly((int) $requestIsValid))
            ->method('getUri')->willReturn('fixtureUrl');
        $requestMock->expects($this->exactly((int) $requestIsValid))
            ->method('getMethod')->willReturn(RequestInterface::METHOD_GET);
        $requestMock->expects($this->exactly((int) $requestIsValid))
            ->method('getOptions')->willReturn([]);

        $this->api = $apiMock;

        if (false === $requestIsValid) {
            $this->expectException(InvalidArgumentException::class);
        }

        $this->assertSame(
            $responseMock,
            $this->callMethod($this->api, 'request', [$requestMock])
        );
    }

    /**
     * @return array
     */
    public function requestPassedDataProvider(): array
    {
        return [
            'request is valid'      => [true],
            'request is not valid'  => [false]
        ];
    }

    /**
     * @test
     * @param $useLogger
     * @param $okStatus
     * @return void
     * @throws ReflectionException
     * @dataProvider rawRequestDataProvider
     */
    public function testRawRequest($useLogger, $okStatus)
    {
        $statusCode = $okStatus ? '200' : '301';

        /** @var StreamInterface|MockObject $streamMock */
        $streamMock = $this->getMockBuilder(StreamInterface::class)
            ->getMock();

        /** @var MessageResponseInterface|MockObject $responseMock */
        $responseMock = $this->getMockBuilder(MessageResponseInterface::class)
            ->onlyMethods([
                'getStatusCode',
                'getBody',
                'withStatus',
                'getReasonPhrase',
                'getProtocolVersion',
                'withProtocolVersion',
                'getHeaders',
                'hasHeader',
                'getHeader',
                'getHeaderLine',
                'withHeader',
                'withAddedHeader',
                'withoutHeader',
                'withBody'
            ])
            ->disableOriginalConstructor()
            ->getMock();
        $responseMock->expects($this->atLeastOnce())->method('getStatusCode')->willReturn($statusCode);
        $responseMock->expects($useLogger && $okStatus ? $this->atLeastOnce() : $this->never())
            ->method('getBody')->willReturn($streamMock);

        /** @var GuzzleClient|MockObject $requestClientMock */
        $requestClientMock = $this->getMockBuilder(GuzzleClient::class)
            ->onlyMethods(['request'])
            ->getMock();
        $requestClientMock->expects($this->once())->method('request')->willReturn($responseMock);

        /** @var LoggerInterface|MockObject $loggerMock */
        $loggerMock = $this->getMockBuilder(AbstractLogger::class)
            ->onlyMethods(['debug', 'error', 'log'])
            ->getMock();

        /** @var Client|MockObject $clientMock */
        $clientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'hasLogger',
                'getLogger'
            ])
            ->getMock();
        $clientMock->method('hasLogger')->willReturn($useLogger);
        $clientMock->expects($useLogger ? $this->atLeastOnce() : $this->never())
            ->method('getLogger')->willReturn($loggerMock);
        $this->setValue($clientMock, 'requestClient', $requestClientMock);

        if (false === $okStatus) {
            $this->expectException(ApiException::class);
        }
        $this->assertSame(
            $responseMock,
            $this->callMethod($clientMock, 'rawRequest', ['myUrl'])
        );
    }

    /**
     * @return array
     */
    public function rawRequestDataProvider(): array
    {
        return [
            'has logger, OK status' => [true, true],
            'has no logger, OK status' => [false, true],
            'has logger, NOK status' => [true, false],
            'has no logger, NOK status' => [false, false],
        ];
    }

    /**
     * @test
     * @return void
     * @throws ReflectionException
     */
    public function testLogger()
    {
        $this->assertFalse($this->callMethod($this->api, 'hasLogger'));
        $this->assertNull($this->callMethod($this->api, 'getLogger'));

        /** @var LoggerInterface|MockObject $loggerMock */
        $loggerMock = $this->getMockBuilder(AbstractLogger::class)
            ->onlyMethods(['debug', 'error', 'log'])
            ->getMock();

        $this->assertSame(
            $this->api,
            $this->callMethod($this->api, 'setLogger', [$loggerMock])
        );

        $this->assertTrue($this->callMethod($this->api, 'hasLogger'));
        $this->assertSame(
            $loggerMock,
            $this->callMethod($this->api, 'getLogger')
        );
    }
}
