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

namespace D3\LinkmobilityClient\Tests;

use Assert\InvalidArgumentException;
use D3\LinkmobilityClient\Client;
use D3\LinkmobilityClient\Exceptions\ApiException;
use D3\LinkmobilityClient\LoggerHandler;
use D3\LinkmobilityClient\Request\RequestInterface;
use D3\LinkmobilityClient\Response\Response;
use D3\LinkmobilityClient\Response\ResponseInterface;
use D3\LinkmobilityClient\SMS\TextRequest;
use D3\LinkmobilityClient\Url\Url;
use D3\LinkmobilityClient\Url\UrlInterface;
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
    public function setUp(): void
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
     * @covers \D3\LinkmobilityClient\Client::__construct
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
     * @covers \D3\LinkmobilityClient\Client::request
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
     * @param $okStatus
     * @return void
     * @throws ReflectionException
     * @dataProvider rawRequestDataProvider
     * @covers \D3\LinkmobilityClient\Client::rawRequest
     */
    public function testRawRequest($okStatus)
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
        $responseMock->expects($this->atLeastOnce())
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

        /** @var LoggerHandler|MockObject $loggerHandlerMock */
        $loggerHandlerMock = $this->getMockBuilder(LoggerHandler::class)
            ->onlyMethods(['getLogger'])
            ->getMock();
        $loggerHandlerMock->method('getLogger')->willReturn($loggerMock);

        /** @var Client|MockObject $clientMock */
        $clientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getLoggerHandler'
            ])
            ->getMock();
        $clientMock->expects($this->atLeastOnce())
            ->method('getLoggerHandler')->willReturn($loggerHandlerMock);
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
            'OK status' => [true],
            'NOK status' => [false],
        ];
    }

    /**
     * @test
     * @return void
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\Client::getLoggerHandler
     */
    public function testGetLoggerHandler()
    {
        $this->assertInstanceOf(
            LoggerHandler::class,
            $this->callMethod(
                $this->api,
                'getLoggerHandler'
            )
        );
    }
}
