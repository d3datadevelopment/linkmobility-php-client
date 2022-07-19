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
use D3\LinkmobilityClient\ValueObject\Sender;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;

class SenderTest extends ApiTestCase
{
    /** @var Sender */
    public $sender;

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
        $this->phoneNumberFixture = $phoneUtil->format($example, PhoneNumberFormat::NATIONAL);

        /** @var Sender|MockObject sender */
        $this->sender = new Sender($this->phoneNumberFixture, $this->phoneCountryFixture);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        unset($this->sender);
    }

    /**
     * @test
     * @return void
     * @throws NumberParseException
     * @throws RecipientException
     * @throws ReflectionException
     * @dataProvider constructValidDataProvider
     * @covers \D3\LinkmobilityClient\ValueObject\Sender::__construct
     */
    public function testConstructValid($number, $country, $hasNumber)
    {
        /** @var PhoneNumberUtil|MockObject $phoneNumberUtilMock */
        $phoneNumberUtilMock = $this->getMockBuilder(PhoneNumberUtil::class)
            ->onlyMethods(['parse', 'format', 'isValidNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $phoneNumberUtilMock->method('parse')->willReturn(new PhoneNumber());
        $phoneNumberUtilMock->method('format')->willReturn('4915792300219');
        $phoneNumberUtilMock->method('isValidNumber')->willReturn(true);

        /** @var Sender|MockObject $senderMock */
        $senderMock = $this->getMockBuilder(Sender::class)
            ->onlyMethods(['getPhoneNumberUtil'])
            ->disableOriginalConstructor()
            ->getMock();
        $senderMock->method('getPhoneNumberUtil')->willReturn($phoneNumberUtilMock);
        $senderMock->__construct($number, $country);

        $this->assertSame(
            $hasNumber,
            $this->callMethod(
                $senderMock,
                'get'
            ) === '4915792300219'
        );
    }

    /**
     * @return array[]
     */
    public function constructValidDataProvider(): array
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $example = $phoneUtil->getExampleNumberForType($this->phoneCountryFixture, PhoneNumberType::MOBILE);
        $this->phoneNumberFixture = $phoneUtil->format($example, PhoneNumberFormat::NATIONAL);

        return [
            'null number'           => [null, $this->phoneCountryFixture, false],
            'null country'          => [$this->phoneNumberFixture, null, false],
            'all values'            => [$this->phoneNumberFixture, $this->phoneCountryFixture, true],
        ];
    }

    /**
     * @test
     * @param $number
     * @param $country
     * @param $validNumber
     * @param $expectedException
     * @throws ReflectionException
     * @dataProvider constructInvalidDataProvider
     * @covers       \D3\LinkmobilityClient\ValueObject\Sender::__construct
     */
    public function testConstructInvalid($number, $country, $validNumber, $expectedException)
    {
        /** @var PhoneNumberUtil|MockObject $phoneNumberUtilMock */
        $phoneNumberUtilMock = $this->getMockBuilder(PhoneNumberUtil::class)
            ->onlyMethods(['parse', 'format', 'isValidNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        if ($number === 'abc') {
            $phoneNumberUtilMock->expects($this->exactly((int) ($country !== 'DEX')))->method('parse')
                ->willThrowException(new NumberParseException(0, 'message'));
        } else {
            $phoneNumberUtilMock->expects($this->exactly((int) ($country !== 'DEX')))->method('parse')
                ->willReturn(new PhoneNumber());
        }
        $phoneNumberUtilMock->method('format')->willReturn($number);
        $phoneNumberUtilMock->method('isValidNumber')->willReturn((bool) $validNumber);

        /** @var Sender|MockObject $senderMock */
        $senderMock = $this->getMockBuilder(Sender::class)
            ->onlyMethods(['getPhoneNumberUtil'])
            ->disableOriginalConstructor()
            ->getMock();
        $senderMock->method('getPhoneNumberUtil')->willReturn($phoneNumberUtilMock);

        $this->expectException($expectedException);

        $this->callMethod(
            $senderMock,
            '__construct',
            [$number, $country]
        );
    }

    /**
     * @return string[][]
     */
    public function constructInvalidDataProvider(): array
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $example = $phoneUtil->getExampleNumberForType($this->phoneCountryFixture, PhoneNumberType::MOBILE);
        $phoneNumberFixture = $phoneUtil->format($example, PhoneNumberFormat::NATIONAL);

        return [
            'empty number'          => ['', 'DE', true, InvalidArgumentException::class],
            'invalid country code'  => [$phoneNumberFixture, 'DEX', true, InvalidArgumentException::class],
            'unparsable'            => ['abc', 'DE', true, NumberParseException::class],
            'invalid number'        => ['abcd', 'DE', false, RecipientException::class],
        ];
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\ValueObject\Sender::getPhoneNumberUtil
     */
    public function testGetPhoneNumberUtil()
    {
        $this->assertInstanceOf(
            PhoneNumberUtil::class,
            $this->callMethod(
                $this->sender,
                'getPhoneNumberUtil'
            )
        );
    }

    /**
     * @test
     * @return void
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\ValueObject\ValueObject::get
     * @covers \D3\LinkmobilityClient\ValueObject\StringValueObject::get
     * @covers \D3\LinkmobilityClient\ValueObject\Sender::get
     */
    public function testGet()
    {
        $this->assertSame(
            '+4915123456789',
            $this->callMethod(
                $this->sender,
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
     * @covers \D3\LinkmobilityClient\ValueObject\Sender::getFormatted
     */
    public function testGetFormatted()
    {
        $this->assertSame(
            '4915123456789',
            $this->callMethod(
                $this->sender,
                'getFormatted'
            )
        );
    }
}
