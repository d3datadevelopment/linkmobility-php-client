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
        $this->phoneNumberFixture = $phoneUtil->format($example,  PhoneNumberFormat::NATIONAL);

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
     */
    public function testConstructValid()
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
        $senderMock->__construct($this->phoneNumberFixture, $this->phoneCountryFixture);

        $this->assertSame(
            '4915792300219',
            $this->callMethod(
                $senderMock,
                'get'
            )
        );
    }

    /**
     * @test
     * @param $number
     * @param $country
     * @param $validNumber
     * @param $expectedException
     *
     * @throws NumberParseException
     * @throws RecipientException
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

        /** @var Sender|MockObject $senderMock */
        $senderMock = $this->getMockBuilder(Sender::class)
            ->onlyMethods(['getPhoneNumberUtil'])
            ->disableOriginalConstructor()
            ->getMock();
        $senderMock->method('getPhoneNumberUtil')->willReturn($phoneNumberUtilMock);

        $this->expectException($expectedException);
        $senderMock->__construct($number, $country);
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
                $this->sender,
                'getPhoneNumberUtil'
            )
        );
    }
}
