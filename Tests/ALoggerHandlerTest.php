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

use D3\LinkmobilityClient\LoggerHandler;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use ReflectionException;

class ALoggerHandlerTest extends ApiTestCase
{
    /** tests must run as first, because of singleton, which must not initialized before */

    /** @var LoggerHandler */
    public $loggerHandler;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->loggerHandler = new LoggerHandler();
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        unset($this->loggerHandler);
    }

    /**
     * @test
     * @return void
     * @covers \D3\LinkmobilityClient\LoggerHandler::getInstance
     */
    public function testGetInstance()
    {
        // not existing instance
        $this->assertInstanceOf(
            LoggerHandler::class,
            LoggerHandler::getInstance()
        );

        // existing instance
        $this->assertInstanceOf(
            LoggerHandler::class,
            LoggerHandler::getInstance()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\LoggerHandler::__construct
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(
            LoggerInterface::class,
            $this->callMethod($this->loggerHandler, 'getLogger')
        );
    }

    /**
     * @test
     * @return void
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\LoggerHandler::setLogger
     * @covers \D3\LinkmobilityClient\LoggerHandler::getLogger
     */
    public function testLogger()
    {
        /** @var LoggerInterface|MockObject $loggerMock */
        $loggerMock = $this->getMockBuilder(AbstractLogger::class)
            ->onlyMethods(['debug', 'error', 'log'])
            ->getMock();

        $this->callMethod($this->loggerHandler, 'setLogger', [ $loggerMock]);
        $this->assertSame(
            $loggerMock,
            $this->callMethod($this->loggerHandler, 'getLogger')
        );
    }
}
