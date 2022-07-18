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
use D3\LinkmobilityClient\Exceptions\NoSenderDefinedException;
use D3\LinkmobilityClient\Exceptions\RecipientException;
use D3\LinkmobilityClient\Tests\ApiTestCase;
use D3\LinkmobilityClient\ValueObject\Sender;
use D3\LinkmobilityClient\ValueObject\ValueObject;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;

class ValueObjectTest extends ApiTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        /** @var ValueObject|MockObject value */
        $this->value = $this->getMockBuilder(ValueObject::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     * @return void
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\ValueObject\ValueObject::__construct
     * @covers \D3\LinkmobilityClient\ValueObject\ValueObject::get
     */
    public function testConstructValid()
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $example = $phoneUtil->getExampleNumberForType('DE', PhoneNumberType::MOBILE);
        $phoneNumberFixture = $phoneUtil->format($example, PhoneNumberFormat::NATIONAL);

        $this->callMethod(
            $this->value,
            '__construct',
            [$phoneNumberFixture]
        );

        $this->assertSame(
            '01512 3456789',
            $this->getValue(
                $this->value,
                'value'
            )
        );
    }

    /**
     * @test
     * @return void
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\ValueObject\ValueObject::__construct
     */
    public function testConstructInvalid()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->callMethod(
            $this->value,
            '__construct',
            ['']
        );
    }
}
