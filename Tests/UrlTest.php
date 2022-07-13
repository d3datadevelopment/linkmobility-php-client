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

use D3\LinkmobilityClient\Url;
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
}
