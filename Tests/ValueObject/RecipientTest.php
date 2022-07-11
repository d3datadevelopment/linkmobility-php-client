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

namespace D3\LinkmobilityClient\Tests\ValueObject;

use Assert\InvalidArgumentException;
use D3\LinkmobilityClient\Tests\ApiTestCase;
use D3\LinkmobilityClient\ValueObject\Recipient;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;

class RecipientTest extends ApiTestCase
{
    /** @var Recipient */
    public $recipient;

    private $phoneNumberFixture = '01527565839';
    private $phoneCountryFixture = 'DE';

    /**
     * @return void
     * @throws NumberParseException
     */
    public function setUp():void
    {
        parent::setUp();

        $this->recipient = new Recipient($this->phoneNumberFixture, $this->phoneCountryFixture);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        unset($this->recipient);
    }

    /**
     * @test
     * @return void
     * @throws NumberParseException
     * @throws ReflectionException
     */
    public function testConstructValid()
    {
        /** @var PhoneNumberUtil|MockObject $phoneNumberUtilMock */
        $phoneNumberUtilMock = $this->getMockBuilder(PhoneNumberUtil::class)
            ->onlyMethods(['parse', 'format'])
            ->disableOriginalConstructor()
            ->getMock();
        $phoneNumberUtilMock->method('parse')->willReturn(new PhoneNumber());
        $phoneNumberUtilMock->method('format')->willReturn('+491527565839');

        /** @var Recipient|MockObject $recipientMock */
        $recipientMock = $this->getMockBuilder(Recipient::class)
            ->onlyMethods(['getPhoneNumberUtil'])
            ->disableOriginalConstructor()
            ->getMock();
        $recipientMock->method('getPhoneNumberUtil')->willReturn($phoneNumberUtilMock);
        $recipientMock->__construct($this->phoneNumberFixture, $this->phoneCountryFixture);

        $this->assertSame(
            '491527565839',
            $this->callMethod(
                $recipientMock,
                'get'
            )
        );

        $this->assertSame(
            $this->phoneCountryFixture,
            $this->callMethod(
                $recipientMock,
                'getCountryCode'
            )
        );
    }

    /**
     * @test
     *
     * @param $number
     * @param $country
     * @param $validNumber
     * @param $expectedException
     *
     * @return void
     * @throws NumberParseException
     * @dataProvider constructInvalidDataProvider
     */
    public function testConstructInvalid($number, $country, $validNumber, $expectedException)
    {
        /** @var PhoneNumberUtil|MockObject $phoneNumberUtilMock */
        $phoneNumberUtilMock = $this->getMockBuilder(PhoneNumberUtil::class)
            ->onlyMethods(['parse', 'format', 'isValidNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        if ($number === 'abc') {
            $phoneNumberUtilMock->method( 'parse' )->willThrowException(new NumberParseException(0, 'message'));
        } else {
            $phoneNumberUtilMock->method( 'parse' )->willReturn( new PhoneNumber() );
        }
        $phoneNumberUtilMock->method('format')->willReturn($number);
        $phoneNumberUtilMock->method('isValidNumber')->willReturn($validNumber);

        /** @var Recipient|MockObject $recipientMock */
        $recipientMock = $this->getMockBuilder(Recipient::class)
            ->onlyMethods(['getPhoneNumberUtil'])
            ->disableOriginalConstructor()
            ->getMock();
        $recipientMock->method('getPhoneNumberUtil')->willReturn($phoneNumberUtilMock);

        $this->expectException($expectedException);
        $recipientMock->__construct($number, $country);
    }

    /**
     * @return string[][]
     */
    public function constructInvalidDataProvider(): array
    {
        return [
            'empty number'          => ['', 'DE', true, InvalidArgumentException::class],
            'invalid country code'  => [$this->phoneNumberFixture, 'DEX', true, InvalidArgumentException::class],
            'unparsable'            => ['abc', 'DE', true, NumberParseException::class],
            'invalid number'        => ['abc', 'DE', false, NumberParseException::class]
        ];
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function testGetPhoneNumberUtil()
    {
        $this->assertInstanceOf(
            PhoneNumberUtil::class,
            $this->callMethod(
                $this->recipient,
                'getPhoneNumberUtil'
            )
        );
    }
}