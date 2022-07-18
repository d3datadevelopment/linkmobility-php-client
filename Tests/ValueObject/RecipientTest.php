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
use D3\LinkmobilityClient\Exceptions\RecipientException;
use D3\LinkmobilityClient\Tests\ApiTestCase;
use D3\LinkmobilityClient\ValueObject\Recipient;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;

class RecipientTest extends ApiTestCase
{
    /** @var Recipient */
    public $recipient;

    private $phoneNumberFixture;
    private $phoneCountryFixture = 'DE';

    /**
     * @return void
     * @throws NumberParseException
     * @throws RecipientException
     */
    public function setUp(): void
    {
        parent::setUp();

        $phoneUtil = PhoneNumberUtil::getInstance();
        $example = $phoneUtil->getExampleNumberForType($this->phoneCountryFixture, PhoneNumberType::MOBILE);
        $this->phoneNumberFixture = $phoneUtil->format($example,  PhoneNumberFormat::NATIONAL);

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
     * @throws RecipientException
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\ValueObject\Recipient::__construct
     */
    public function testConstructValid()
    {
        /** @var PhoneNumberUtil|MockObject $phoneNumberUtilMock */
        $phoneNumberUtilMock = $this->getMockBuilder(PhoneNumberUtil::class)
            ->onlyMethods(['parse', 'format', 'isValidNumber', 'getNumberType'])
            ->disableOriginalConstructor()
            ->getMock();
        $phoneNumberUtilMock->method('parse')->willReturn(new PhoneNumber());
        $phoneNumberUtilMock->method('format')->willReturn('+491527565839');
        $phoneNumberUtilMock->method('isValidNumber')->willReturn(true);
        $phoneNumberUtilMock->method('getNumberType')->willReturn(PhoneNumberType::MOBILE);

        /** @var Recipient|MockObject $recipientMock */
        $recipientMock = $this->getMockBuilder(Recipient::class)
            ->onlyMethods(['getPhoneNumberUtil'])
            ->disableOriginalConstructor()
            ->getMock();
        $recipientMock->method('getPhoneNumberUtil')->willReturn($phoneNumberUtilMock);
        $recipientMock->__construct($this->phoneNumberFixture, $this->phoneCountryFixture);

        $this->assertSame(
            '+491527565839',
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
     * @param $numberType
     * @param $expectedException
     *
     * @return void
     * @throws NumberParseException
     * @throws RecipientException
     * @dataProvider constructInvalidDataProvider
     * @covers       \D3\LinkmobilityClient\ValueObject\Recipient::__construct
     */
    public function testConstructInvalid($number, $country, $validNumber, $numberType, $expectedException)
    {
        /** @var PhoneNumberUtil|MockObject $phoneNumberUtilMock */
        $phoneNumberUtilMock = $this->getMockBuilder(PhoneNumberUtil::class)
            ->onlyMethods(['parse', 'format', 'isValidNumber', 'getNumberType'])
            ->disableOriginalConstructor()
            ->getMock();
        if ($number === 'abc') {
            $phoneNumberUtilMock->method('parse')->willThrowException(new NumberParseException(0, 'message'));
        } else {
            $phoneNumberUtilMock->method('parse')->willReturn(new PhoneNumber());
        }
        $phoneNumberUtilMock->method('format')->willReturn($number);
        $phoneNumberUtilMock->method('isValidNumber')->willReturn($validNumber);
        $phoneNumberUtilMock->method('getNumberType')->willReturn($numberType);

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
        $phoneUtil = PhoneNumberUtil::getInstance();
        $example = $phoneUtil->getExampleNumberForType($this->phoneCountryFixture, PhoneNumberType::MOBILE);
        $phoneNumberFixture = $phoneUtil->format($example,  PhoneNumberFormat::NATIONAL);

        return [
            'empty number'          => ['', 'DE', true, PhoneNumberType::MOBILE, InvalidArgumentException::class],
            'invalid country code'  => [$phoneNumberFixture, 'DEX', true, PhoneNumberType::MOBILE, InvalidArgumentException::class],
            'unparsable'            => ['abc', 'DE', true, PhoneNumberType::MOBILE, NumberParseException::class],
            'invalid number'        => ['abcd', 'DE', false, PhoneNumberType::MOBILE, RecipientException::class],
            'not mobile number'     => ['abcd', 'DE', true, PhoneNumberType::FIXED_LINE, RecipientException::class]
        ];
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\ValueObject\Recipient::getPhoneNumberUtil
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

    /**
     * @test
     * @return void
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\ValueObject\Recipient::getCountryCode
     */
    public function testGetCountryCode()
    {
        $this->assertSame(
            $this->phoneCountryFixture,
            $this->callMethod(
                $this->recipient,
                'getCountryCode'
            )
        );
    }

    /**
     * @test
     * @return void
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\ValueObject\ValueObject::get
     * @covers \D3\LinkmobilityClient\ValueObject\StringValueObject::get
     * @covers \D3\LinkmobilityClient\ValueObject\StringValueObject::__toString
     * @covers \D3\LinkmobilityClient\ValueObject\Recipient::get
     */
    public function testGet()
    {
        $this->assertSame(
            '+4915123456789',
            (string) $this->recipient
        );

        $this->assertSame(
            '+4915123456789',
            $this->callMethod(
                $this->recipient,
                'get'
            )
        );
    }

    /**
     * @test
     * @return void
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\ValueObject\ValueObject::getFormatted
     * @covers \D3\LinkmobilityClient\ValueObject\StringValueObject::getFormatted
     * @covers \D3\LinkmobilityClient\ValueObject\Recipient::getFormatted
     */
    public function testGetFormatted()
    {
        $this->assertSame(
            '4915123456789',
            $this->callMethod(
                $this->recipient,
                'getFormatted'
            )
        );
    }
}
