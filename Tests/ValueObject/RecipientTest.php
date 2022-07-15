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
            $phoneNumberUtilMock->method('parse')->willThrowException(new NumberParseException(0, 'message'));
        } else {
            $phoneNumberUtilMock->method('parse')->willReturn(new PhoneNumber());
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
        $phoneUtil = PhoneNumberUtil::getInstance();
        $example = $phoneUtil->getExampleNumberForType($this->phoneCountryFixture, PhoneNumberType::MOBILE);
        $phoneNumberFixture = $phoneUtil->format($example,  PhoneNumberFormat::NATIONAL);

        return [
            'empty number'          => ['', 'DE', true, InvalidArgumentException::class],
            'invalid country code'  => [$phoneNumberFixture, 'DEX', true, InvalidArgumentException::class],
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
