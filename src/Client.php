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

namespace D3\LinkmobilityClient;

use D3\LinkmobilityClient\Exceptions\ApiException;
use D3\LinkmobilityClient\Request\RequestInterface;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class Client
{
    private $accessToken;
    public $apiUrl;
    public $requestClient;

    public function __construct(string $accessToken, $apiUrl = false, $client = false)
    {
        if ($apiUrl !== false && false === $apiUrl instanceof UrlInterface) {
            throw new \RuntimeException('ApiUrl instance must implement UrlInterface');
        }

        $this->accessToken = $accessToken;
        $this->apiUrl = $apiUrl ?: new Url();
        $this->requestClient = $client ?: new \GuzzleHttp\Client( [ 'base_uri' => $this->apiUrl->getBaseUri() ] );
    }

    public function request(RequestInterface $request) : \D3\LinkmobilityClient\Response\ResponseInterface
    {
        $request->validate();
        $responseClass = $request->getResponseClass();

        return $request->getResponseInstance(
            $this->rawRequest($request->getUri(), $request->getMethod(), $request->getOptions())
        );
    }

    /**
     * @param        $url
     * @param string $method
     * @param array  $postArgs
     *
     * @return ResponseInterface
     * @throws ApiException
     * @throws GuzzleException
     */
    protected function rawRequest( $url, string $method = RequestInterface::METHOD_GET, array $options = []): ResponseInterface
    {
        $options['headers']['Authorization'] = 'Bearer '.$this->accessToken;

        $response = $this->requestClient->request(
            $method,
            $url,
            $options
        );

        if ($response->getStatusCode() != 200) {
            throw new ApiException('request '.$url.' returns status code '.$response->getStatusCode());
        }

        return $response;
    }
}