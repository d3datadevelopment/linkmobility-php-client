<?php

namespace D3\LinkmobilityClient\Tests;

use D3\LinkmobilityClient\Client;

class ApiClientTest extends ApiTestCase
{
    public string $fixtureApiKey = 'fixtureApiKey';
    /** @var ApiClient */
    public Client $api;
    public string $jsonFixture = 'json_content';

    public function setUp():void
    {
        parent::setUp();

        $this->api = new Client($this->fixtureApiKey);
    }
    
    /**
     * @test
     */
    public function testRun()
    {
        $this->assertTrue(true);
    }
}
