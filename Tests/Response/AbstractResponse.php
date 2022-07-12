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
                ]
            ,
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
     */
    public function testGetInternalStatus()
    {
        $this->checkProperties(200, 'statusCode', 'getInternalStatus');
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function testGetStatusMessage()
    {
        $this->checkProperties('statusMessage', 'statusMessage', 'getStatusMessage');
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function testGetTransferId()
    {
        $this->checkProperties('transferId', 'transferId', 'getTransferId');
    }

    /**
     * @test
     * @throws ReflectionException
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