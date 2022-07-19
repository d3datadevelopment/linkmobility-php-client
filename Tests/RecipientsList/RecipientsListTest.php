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

namespace D3\LinkmobilityClient\Tests\RecipientsList;

use D3\LinkmobilityClient\Client;
use D3\LinkmobilityClient\RecipientsList\RecipientsList;
use D3\LinkmobilityClient\Tests\ApiTestCase;
use D3\LinkmobilityClient\ValueObject\Recipient;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;
use stdClass;

class RecipientsListTest extends ApiTestCase
{
    /** @var RecipientsList */
    public $recipientsList;

    private $phoneNumberFixture;
    private $phoneCountryFixture = 'DE';

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        /** @var Client|MockObject $clientMock */
        $clientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->recipientsList = new RecipientsList($clientMock);

        $phoneUtil = PhoneNumberUtil::getInstance();
        $example = $phoneUtil->getExampleNumberForType($this->phoneCountryFixture, PhoneNumberType::MOBILE);
        $this->phoneNumberFixture = $phoneUtil->format($example, PhoneNumberFormat::NATIONAL);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        unset($this->recipientsList);
    }

    /**
     * @test
     * @return void
     * @covers \D3\LinkmobilityClient\RecipientsList\RecipientsList::__construct
     */
    public function testConstruct()
    {
        /** @var Client|MockObject $clientMock */
        $clientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->recipientsList = new RecipientsList($clientMock);

        $this->assertSame(
            $this->recipientsList->getClient(),
            $clientMock
        );
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\RecipientsList\RecipientsList::add
     */
    public function testAddValidNumber()
    {
        /** @var Recipient|MockObject $recipientMock */
        $recipientMock = $this->getMockBuilder(Recipient::class)
            ->onlyMethods(['get', 'getCountryCode'])
            ->setConstructorArgs([$this->phoneNumberFixture, $this->phoneCountryFixture])
            ->getMock();
        $recipientMock->method('get')->willReturn($this->phoneNumberFixture);
        $recipientMock->method('getCountryCode')->willReturn($this->phoneCountryFixture);

        $this->assertCount(
            0,
            $this->callMethod($this->recipientsList, 'getRecipientsList')
        );

        $this->assertSame(
            $this->recipientsList,
            $this->callMethod(
                $this->recipientsList,
                'add',
                [$recipientMock]
            )
        );

        $this->assertCount(
            1,
            $this->callMethod($this->recipientsList, 'getRecipientsList')
        );
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\RecipientsList\RecipientsList::clearRecipents
     * @covers \D3\LinkmobilityClient\RecipientsList\RecipientsList::getRecipientsList
     */
    public function testClearRecipents()
    {
        $this->addRecipientFixture();

        $this->assertCount(
            2,
            $this->callMethod($this->recipientsList, 'getRecipientsList')
        );

        $this->assertInstanceOf(
            RecipientsList::class,
            $this->callMethod(
                $this->recipientsList,
                'clearRecipents'
            )
        );

        $this->assertCount(
            0,
            $this->callMethod($this->recipientsList, 'getRecipientsList')
        );
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\RecipientsList\RecipientsList::getRecipients
     */
    public function testGetRecipients()
    {
        $this->assertCount(
            0,
            $this->callMethod(
                $this->recipientsList,
                'getRecipients'
            )
        );

        $this->addRecipientFixture();

        $this->assertSame(
            [
                $this->phoneNumberFixture,
                $this->phoneNumberFixture,
            ],
            $this->callMethod(
                $this->recipientsList,
                'getRecipients'
            )
        );
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\RecipientsList\RecipientsList::getRecipientsList
     */
    public function testGetRecipientsList()
    {
        $this->assertCount(
            0,
            $this->callMethod(
                $this->recipientsList,
                'getRecipientsList'
            )
        );

        $this->addRecipientFixture();

        $recipientsList = $this->callMethod(
            $this->recipientsList,
            'getRecipientsList'
        );

        $this->assertCount(
            2,
            $recipientsList
        );
        $this->assertInstanceOf(
            Recipient::class,
            $recipientsList['fixture']
        );
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\RecipientsList\RecipientsList::current
     * @covers \D3\LinkmobilityClient\RecipientsList\RecipientsList::next
     * @covers \D3\LinkmobilityClient\RecipientsList\RecipientsList::key
     * @covers \D3\LinkmobilityClient\RecipientsList\RecipientsList::rewind
     */
    public function testCurrentNextKeyRewind()
    {
        $list = $this->addRecipientFixture();

        $current = $this->callMethod(
            $this->recipientsList,
            'current'
        );

        $this->assertInstanceOf(
            Recipient::class,
            $current
        );

        $this->assertSame(
            $list['fixture2'],
            $this->callMethod(
                $this->recipientsList,
                'next'
            )
        );

        $this->assertSame(
            'fixture2',
            $this->callMethod(
                $this->recipientsList,
                'key'
            )
        );

        $this->callMethod(
            $this->recipientsList,
            'rewind'
        );

        $this->assertSame(
            'fixture',
            $this->callMethod(
                $this->recipientsList,
                'key'
            )
        );
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\RecipientsList\RecipientsList::valid
     */
    public function testValid()
    {
        $this->addRecipientFixture();

        $this->assertTrue(
            $this->callMethod(
                $this->recipientsList,
                'valid'
            )
        );

        $this->setValue(
            $this->recipientsList,
            'recipients',
            [
                'fixture' => new stdClass(),
            ]
        );

        $this->assertFalse(
            $this->callMethod(
                $this->recipientsList,
                'valid'
            )
        );
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\RecipientsList\RecipientsList::setClient
     * @covers \D3\LinkmobilityClient\RecipientsList\RecipientsList::getClient
     */
    public function testSetGetClient()
    {
        /** @var Client|MockObject $clientMock */
        $clientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertInstanceOf(
            RecipientsList::class,
            $this->callMethod(
                $this->recipientsList,
                'setClient',
                [$clientMock]
            )
        );

        $this->assertSame(
            $clientMock,
            $this->callMethod(
                $this->recipientsList,
                'getClient'
            )
        );
    }

    /**
     * @return Recipient[]|MockObject[]
     * @throws ReflectionException
     */
    protected function addRecipientFixture(): array
    {
        /** @var Recipient|MockObject $recipientMock */
        $recipientMock = $this->getMockBuilder(Recipient::class)
            ->onlyMethods([
                'get',
                'getCountryCode',
            ])
            ->setConstructorArgs([
                $this->phoneNumberFixture,
                $this->phoneCountryFixture,
            ])
            ->getMock();
        $recipientMock->method('get')->willReturn($this->phoneNumberFixture);
        $recipientMock->method('getCountryCode')->willReturn($this->phoneCountryFixture);

        /** @var Recipient|MockObject $recipientMock2 */
        $recipientMock2 = $this->getMockBuilder(Recipient::class)
            ->onlyMethods([
                'get',
                'getCountryCode',
            ])
            ->setConstructorArgs([
                $this->phoneNumberFixture,
                $this->phoneCountryFixture,
            ])
            ->getMock();
        $recipientMock2->method('get')->willReturn($this->phoneNumberFixture);
        $recipientMock2->method('getCountryCode')->willReturn($this->phoneCountryFixture);

        $list = [
            'fixture' => $recipientMock,
            'fixture2' => $recipientMock2,
        ];

        $this->setValue(
            $this->recipientsList,
            'recipients',
            $list
        );

        return $list;
    }
}
