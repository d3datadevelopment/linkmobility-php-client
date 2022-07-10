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

namespace D3\LinkmobilityClient\Tests;

use D3\LinkmobilityClient\Url;
use ReflectionException;

class UrlTest extends ApiTestCase
{
    /** @var Url */
    public Url $url;

    /**
     * @return void
     */
    public function setUp():void
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
