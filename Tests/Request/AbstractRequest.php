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

namespace D3\LinkmobilityClient\Tests\Request;

use Assert\InvalidArgumentException;
use D3\LinkmobilityClient\Client;
use D3\LinkmobilityClient\Exceptions\RecipientException;
use D3\LinkmobilityClient\RecipientsList\RecipientsListInterface;
use D3\LinkmobilityClient\Request\Request;
use D3\LinkmobilityClient\Request\RequestInterface;
use D3\LinkmobilityClient\SMS\Response;
use D3\LinkmobilityClient\Tests\ApiTestCase;
use D3\LinkmobilityClient\ValueObject\Recipient;
use D3\LinkmobilityClient\ValueObject\Sender;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use ReflectionException;

abstract class AbstractRequest extends ApiTestCase
{
    /** @var Request */
    protected $request;
    protected $testClassName;
    protected $messageClassName;

    public function setUp(): void
    {
        parent::setUp();

        $messageMock = $this->getMockBuilder($this->messageClassName)
                            ->disableOriginalConstructor()
                            ->getMock();
        /** @var Client|MockObject $clientMock */
        $clientMock = $this->getMockBuilder(Client::class)
                           ->disableOriginalConstructor()
                           ->getMock();

        $this->request = new $this->testClassName($messageMock, $clientMock);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        unset($this->request);
    }

    /**
     * @test
     * @return void
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::__construct
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::__construct
     */
    public function testConstruct()
    {
        $messageMock = $this->getMockBuilder($this->messageClassName)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Client|MockObject $clientMock */
        $clientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->getMockBuilder($this->testClassName)
            ->disableOriginalConstructor()
            ->onlyMethods(['setMessage', 'setClient'])
            ->getMock();
        $requestMock->expects($this->atLeastOnce())->method('setMessage')->with($this->equalTo($messageMock))->willReturnSelf();
        $requestMock->expects($this->atLeastOnce())->method('setClient')->with($this->equalTo($clientMock))->willReturnSelf();

        $this->assertInstanceOf(
            Request::class,
            $this->callMethod(
                $requestMock,
                '__construct',
                [$messageMock, $clientMock]
            )
        );
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getUri
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getUri
     */
    public function testGetUri()
    {
        $this->assertIsString(
            $this->callMethod(
                $this->request,
                'getUri'
            )
        );
    }

    /**
     * @test
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::validate
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::validate
     * @covers \D3\LinkmobilityClient\Request\Request::validate
     * @return void
     * @throws ReflectionException
     * @throws RecipientException
     * @throws NumberParseException
     */
    public function validatePassedTest()
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $example = $phoneUtil->getExampleNumberForType('DE', PhoneNumberType::MOBILE);
        $phoneNumberFixture = $phoneUtil->format($example, PhoneNumberFormat::NATIONAL);

        $recipient = new Recipient($phoneNumberFixture, 'DE');

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->getMockBuilder($this->testClassName)
            ->setConstructorArgs([$this->request->getMessage(), $this->request->getClient()])
            ->onlyMethods(['getBody', 'getMethod', 'getUri', 'getResponseClass', 'getOptions'])
            ->getMock();
        $requestMock->expects($this->atLeastOnce())->method('getMethod')->willReturn(RequestInterface::METHOD_GET);
        $requestMock->expects($this->atLeastOnce())->method('getBody')->willReturn(['fixture']);
        $requestMock->expects($this->atLeastOnce())->method('getUri')->willReturn('/uri');
        $requestMock->expects($this->atLeastOnce())->method('getResponseClass')->willReturn(Response::class);
        $requestMock->expects($this->atLeastOnce())->method('getOptions')->willReturn([]);

        $requestMock->getRecipientsList()->add($recipient);

        $this->callMethod(
            $requestMock,
            'validate'
        );
    }

    /**
     * @test
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::validate
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::validate
     * @return void
     * @throws NumberParseException
     * @throws RecipientException
     * @throws ReflectionException
     */
    public function validateFailedTest()
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $example = $phoneUtil->getExampleNumberForType('DE', PhoneNumberType::MOBILE);
        $phoneNumberFixture = $phoneUtil->format($example, PhoneNumberFormat::NATIONAL);

        $recipient = new Recipient($phoneNumberFixture, 'DE');

        /** @var Request|MockObject $requestMock */
        $requestMock = $this->getMockBuilder($this->testClassName)
            ->setConstructorArgs([$this->request->getMessage(), $this->request->getClient()])
            ->onlyMethods(['getBody', 'getMethod', 'getUri', 'getResponseClass', 'getOptions'])
            ->getMock();
        $requestMock->expects($this->atLeastOnce())->method('getMethod')->willReturn('otherMethod');
        $requestMock->expects($this->any())->method('getBody')->willReturn(['fixture']);
        $requestMock->expects($this->any())->method('getUri')->willReturn('/uri');
        $requestMock->expects($this->any())->method('getResponseClass')->willReturn(Response::class);
        $requestMock->expects($this->any())->method('getOptions')->willReturn([]);

        $requestMock->getRecipientsList()->add($recipient);

        $this->expectException(InvalidArgumentException::class);

        $this->callMethod(
            $requestMock,
            'validate'
        );
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getRawBody
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getRawBody
     * @covers \D3\LinkmobilityClient\Request\Request::getRawBody
     */
    public function testGetRawBody()
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->getMockBuilder($this->testClassName)
            ->setConstructorArgs([$this->request->getMessage(), $this->request->getClient()])
            ->onlyMethods(['getMessage', 'getTestMode'])
            ->getMock();
        $requestMock->expects($this->atLeastOnce())->method('getMessage');
        $requestMock->expects($this->atLeastOnce())->method('getTestMode');

        $rawBody = $this->callMethod(
            $requestMock,
            'getRawBody'
        );

        $this->assertIsArray($rawBody);
        $this->assertContains('messageContent', array_keys($rawBody));
    }

    /**
     * @test
     * @throws ReflectionException
     * @dataProvider getBodyDataProvider
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getBody
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getBody
     */
    public function testGetBody($contentType, $expected)
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->getMockBuilder($this->testClassName)
            ->setConstructorArgs([$this->request->getMessage(), $this->request->getClient()])
            ->onlyMethods(['getRawBody', 'getContentType'])
            ->getMock();
        $requestMock->expects($this->atLeastOnce())->method('getRawBody')->willReturn(
            [
                'clientMessageId'   => null,
                'contentCategory'   => 'informational',
                'messageContent'    => 'messageContent',
                'notificationCallbackUrl'   => null,
                'priority'          => 0,
            ]
        );
        $requestMock->expects($this->atLeastOnce())->method('getContentType')->willReturn($contentType);

        $this->assertSame(
            $expected,
            $this->callMethod(
                $requestMock,
                'getBody'
            )
        );
    }

    /**
     * @return array
     */
    public function getBodyDataProvider(): array
    {
        return [
            'json format'   => [
                RequestInterface::CONTENTTYPE_JSON,
                ['json' => [
                    'contentCategory' => 'informational',
                    'messageContent' => 'messageContent',
                    'priority' => 0,
                    ]],
            ],
            'other' => [
                'other',
                [
                    'contentCategory' => 'informational',
                    'messageContent' => 'messageContent',
                    'priority' => 0,
                ],
            ],
        ];
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getOptions
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getOptions
     */
    public function testGetOptions()
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->getMockBuilder($this->testClassName)
            ->setConstructorArgs([$this->request->getMessage(), $this->request->getClient()])
            ->onlyMethods(['getBody'])
            ->getMock();
        $requestMock->expects($this->atLeastOnce())->method('getBody')->willReturn(
            ['json' => [
                'contentCategory' => 'informational',
                'messageContent' => 'messageContent',
                'priority' => 0,
            ]]
        );

        $this->assertSame(
            ['headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'contentCategory' => 'informational',
                'messageContent' => 'messageContent',
                'priority' => 0,
            ], ],
            $this->callMethod(
                $requestMock,
                'getOptions'
            )
        );
    }

    /**
     * @throws ReflectionException
     */
    public function checkGetterSetter($content, $setter, $getter)
    {
        $this->assertInstanceOf(
            Request::class,
            $this->callMethod(
                $this->request,
                $setter,
                [$content]
            )
        );

        $this->assertSame(
            $content,
            $this->callMethod(
                $this->request,
                $getter
            )
        );
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::setMessage
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::setMessage
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getMessage
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getMessage
     */
    public function setGetMessageTest()
    {
        $messageMock = $this->getMockBuilder($this->messageClassName)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkGetterSetter($messageMock, 'setMessage', 'getMessage');
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::setMethod
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::setMethod
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getMethod
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getMethod
     */
    public function setGetMethodTest()
    {
        $this->checkGetterSetter('fixture', 'setMethod', 'getMethod');
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getMethods
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getMethods
     */
    public function getMethodsTest()
    {
        $methods = $this->callMethod(
            $this->request,
            'getMethods'
        );

        $this->assertIsArray($methods);
        $this->assertTrue((bool) count($methods));
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::setContentType
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::setContentType
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getContentType
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getContentType
     */
    public function setGetContentTypeTest()
    {
        $this->checkGetterSetter('fixture', 'setContentType', 'getContentType');
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::setClientMessageId
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::setClientMessageId
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getClientMessageId
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getClientMessageId
     */
    public function setGetClientMessageIdTest()
    {
        $this->checkGetterSetter('fixture', 'setClientMessageId', 'getClientMessageId');
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::setContentCategory
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::setContentCategory
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getContentCategory
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getContentCategory
     */
    public function setGetContentCategoryTest()
    {
        $this->checkGetterSetter('fixture', 'setContentCategory', 'getContentCategory');
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getContentCategories
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getContentCategories
     */
    public function getContentCategoriesTest()
    {
        $contentCategories = $this->callMethod(
            $this->request,
            'getContentCategories'
        );

        $this->assertIsArray($contentCategories);
        $this->assertTrue((bool) count($contentCategories));
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::setTestMode
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::setTestMode
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getTestMode
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getTestMode
     */
    public function setGetTestModeTest()
    {
        $this->checkGetterSetter(true, 'setTestMode', 'getTestMode');
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::setMaxSmsPerMessage
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::setMaxSmsPerMessage
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getMaxSmsPerMessage
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getMaxSmsPerMessage
     */
    public function setGetMaxSmsPerMessageTest()
    {
        $this->checkGetterSetter(2, 'setMaxSmsPerMessage', 'getMaxSmsPerMessage');
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::setMessageType
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::setMessageType
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getMessageType
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getMessageType
     */
    public function setGetMessageTypeTest()
    {
        $this->checkGetterSetter('fixture', 'setMessageType', 'getMessageType');
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::setNotificationCallbackUrl
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::setNotificationCallbackUrl
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getNotificationCallbackUrl
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getNotificationCallbackUrl
     */
    public function setGetNotificationCallbackUrlTest()
    {
        $this->checkGetterSetter('fixture', 'setNotificationCallbackUrl', 'getNotificationCallbackUrl');
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::setPriority
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::setPriority
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getPriority
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getPriority
     */
    public function setGetPriorityTest()
    {
        $this->checkGetterSetter(200, 'setPriority', 'getPriority');
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getRecipientsList
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getRecipientsList
     */
    public function getRecipientsListTest()
    {
        $this->assertInstanceOf(
            RecipientsListInterface::class,
            $this->callMethod(
                $this->request,
                'getRecipientsList'
            )
        );
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::sendAsFlashSms
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::sendAsFlashSms
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::doSendAsFlashSms
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::doSendAsFlashSms
     */
    public function setGetFlashSmsTest()
    {
        $this->checkGetterSetter(true, 'sendAsFlashSms', 'doSendAsFlashSms');
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::setSenderAddress
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::setSenderAddress
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getSenderAddress
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getSenderAddress
     */
    public function setGetSenderAddressTest()
    {
        $senderMock = $this->getMockBuilder(Sender::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkGetterSetter($senderMock, 'setSenderAddress', 'getSenderAddress');
    }

    /**
     * @test
     * @throws ReflectionException
     * @dataProvider setGetSenderAddressTypeDataProvider
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::setSenderAddressType
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::setSenderAddressType
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getSenderAddressType
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getSenderAddressType
     */
    public function testSetGetSenderAddressType($hasSender, $addressType, $expected)
    {
        /** @var Request|MockObject $request */
        $request = $this->getMockBuilder($this->testClassName)
            ->setConstructorArgs([$this->request->getMessage(), $this->request->getClient()])
            ->onlyMethods(['getSenderAddress'])
            ->getMock();

        if ($hasSender) {
            /** @var Sender|MockObject $senderMock */
            $senderMock = $this->getMockBuilder(Sender::class)
                ->disableOriginalConstructor()
                ->onlyMethods(['get'])
                ->getMock();
            $senderMock->method('get')->willReturn('fixture');
            $request->method('getSenderAddress')->willReturn($senderMock);
        } else {
            $request->method('getSenderAddress')->willReturn(null);
        }

        $this->assertInstanceOf(
            Request::class,
            $this->callMethod(
                $request,
                'setSenderAddressType',
                [$addressType]
            )
        );

        $this->assertSame(
            $expected,
            $this->callMethod(
                $request,
                'getSenderAddressType'
            )
        );
    }

    /**
     * @return array[]
     */
    public function setGetSenderAddressTypeDataProvider(): array
    {
        return [
            'has no sender'                 => [false, 'fixture', null],
            'has sender and address type'   => [true, 'fixture', 'fixture'],
        ];
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getSenderAddressTypes
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getSenderAddressTypes
     */
    public function getSenderAddressTypesTest()
    {
        $senderAddressTypes = $this->callMethod(
            $this->request,
            'getSenderAddressTypes'
        );

        $this->assertIsArray($senderAddressTypes);
        $this->assertTrue((bool) count($senderAddressTypes));
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::setValidityPeriode
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::setValidityPeriode
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getValidityPeriode
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getValidityPeriode
     */
    public function setGetValidityPeriodeTest()
    {
        $this->checkGetterSetter(1, 'setValidityPeriode', 'getValidityPeriode');
    }

    /**
     * @test
     * @throws ReflectionException
     * @dataProvider getResponseInstanceDataProvider
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getResponseInstance
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getResponseInstance
     */
    public function testGetResponseInstance(ResponseInterface $response)
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->getMockBuilder(Request::class)
            ->onlyMethods(['getResponseClass', 'getUri'])
            ->disableOriginalConstructor()
            ->getMock();
        $requestMock->expects($this->atLeastOnce())->method('getResponseClass')->willReturn(Response::class);

        $instance = $this->callMethod(
            $requestMock,
            'getResponseInstance',
            [$response]
        );

        $this->assertInstanceOf(
            Response::class,
            $instance
        );
        $this->assertSame(
            $response,
            $instance->getRawResponse()
        );
    }

    /**
     * @return array[]
     */
    public function getResponseInstanceDataProvider(): array
    {
        /** @var StreamInterface|MockObject $streamMock */
        $streamMock = $this->getMockBuilder(StreamInterface::class)
            ->onlyMethods(['getContents', '__toString', 'close', 'detach', 'getSize', 'tell', 'eof', 'isSeekable',
                'seek', 'rewind', 'isWritable', 'write', 'isReadable', 'read', 'getMetadata', ])
            ->getMock();
        $streamMock->method('getContents')->willReturn('{}');

        /** @var ResponseInterface|MockObject $rawResponseMock */
        $rawResponseMock = $this->getMockBuilder(ResponseInterface::class)
            ->onlyMethods([
                'getBody', 'getStatusCode', 'withStatus', 'getReasonphrase', 'getProtocolVersion',
                'withProtocolVersion', 'getHeaders', 'hasHeader', 'getHeader', 'getHeaderLine',
                'withHeader', 'withAddedHeader', 'withoutHeader', 'withBody', ])
            ->getMock();
        $rawResponseMock->method('getBody')->willReturn($streamMock);

        return [
            'SMS Response'  => [$rawResponseMock],
        ];
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::setClient
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::setClient
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getClient
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getClient
     */
    public function setGetClientTest()
    {
        /** @var Client|MockObject $clientMock */
        $clientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkGetterSetter($clientMock, 'setClient', 'getClient');
    }

    /**
     * @test
     * @return void
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\SMS\BinaryRequest::getResponseClass
     * @covers \D3\LinkmobilityClient\SMS\TextRequest::getResponseClass
     */
    public function testGetResponseClass()
    {
        $className = $this->callMethod(
            $this->request,
            'getResponseClass'
        );

        $this->assertIsString(
            $className
        );

        /** @var \D3\LinkmobilityClient\Response\ResponseInterface|MockObject $responseMock */
        $responseMock = $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertInstanceOf(
            \D3\LinkmobilityClient\Response\ResponseInterface::class,
            $responseMock
        );
    }
}
