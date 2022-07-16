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

namespace D3\LinkmobilityClient\Tests\Url;

use D3\LinkmobilityClient\Tests\ApiTestCase;
use D3\LinkmobilityClient\Url\Url;
use ReflectionException;

class UrlTest extends ApiTestCase
{
    /** @var Url */
    public $url;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->url = new Url();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        unset($this->url);
    }

    /**
     * @test
     * @return void
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\Url\Url::getBaseUri
     */
    public function testGetBaseUri()
    {
        $fixture = "fixtureUri";
        $this->setValue($this->url, 'baseUri', $fixture);

        $this->assertSame(
            $fixture,
            $this->callMethod(
                $this->url,
                'getBaseUri'
            )
        );
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\Url\Url::getTextSmsUri
     */
    public function testGetTextSmsUri()
    {
        $uri = $this->callMethod(
            $this->url,
            'getTextSmsUri'
        );

        $this->assertIsString($uri);
        $this->assertStringStartsWith('/', $uri);
    }

    /**
     * @test
     * @throws ReflectionException
     * @covers \D3\LinkmobilityClient\Url\Url::getBinarySmsUri
     */
    public function testGetBinarySmsUri()
    {
        $uri = $this->callMethod(
            $this->url,
            'getBinarySmsUri'
        );

        $this->assertIsString($uri);
        $this->assertStringStartsWith('/', $uri);
    }
}
