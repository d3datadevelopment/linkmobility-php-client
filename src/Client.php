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

namespace D3\LinkmobilityClient;

use Assert\InvalidArgumentException;
use D3\LinkmobilityClient\Request\RequestInterface;
use D3\LinkmobilityClient\Url\Url;
use D3\LinkmobilityClient\Url\UrlInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleRetry\GuzzleRetryMiddleware;
use Psr\Http\Message\ResponseInterface;

class Client
{
    private $accessToken;
    public $apiUrl;
    public $requestClient;

    public function __construct(string $accessToken, UrlInterface $apiUrl = null, ClientInterface $client = null)
    {
        $this->accessToken = $accessToken;
        $this->apiUrl = $apiUrl ?: new Url();
        $this->requestClient = $client ?: $this->getDefaultClient();
    }

    /**
     * @return GuzzleClient
     */
    protected function getDefaultClient(): GuzzleClient
    {
        $handlerStack = HandlerStack::create();
        $handlerStack->push($this->getLoggerMiddleware());
        $handlerStack->push($this->getRetryMiddleware());

        return new GuzzleClient( [
            'base_uri'  => $this->apiUrl->getBaseUri(),
            'handler'   => $handlerStack
        ]);
    }

    /**
     * @param RequestInterface $request
     *
     * @return Response\ResponseInterface
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function request(RequestInterface $request): Response\ResponseInterface
    {
        $request->validate();

        return $request->getResponseInstance(
            $this->rawRequest($request->getUri(), $request->getMethod(), $request->getOptions())
        );
    }

    /**
     * @param        $url
     * @param string $method
     * @param array  $options
     *
     * @return ResponseInterface
     * @throws GuzzleException
     */
    protected function rawRequest($url, string $method = RequestInterface::METHOD_GET, array $options = []): ResponseInterface
    {
        $options['headers']['Authorization'] = 'Bearer '.$this->accessToken;

        $response = $this->requestClient->request($method, $url, $options);
        $response->getBody()->rewind();

        return $response;
    }

    /**
     * @return LoggerHandler
     */
    public function getLoggerHandler(): LoggerHandler
    {
        return LoggerHandler::getInstance();
    }

    /**
     * @param string $loglevel
     *
     * @return callable
     */
    protected function getLoggerMiddleware(string $loglevel = 'debug'): callable
    {
        return Middleware::log(
            $this->getLoggerHandler()->getLogger(),
            new MessageFormatter(MessageFormatter::DEBUG),
            $loglevel
        );
    }

    /**
     * @return callable
     */
    protected function getRetryMiddleware(): callable
    {
        return GuzzleRetryMiddleware::factory([
            'max_retry_attempts' => 3
        ]);
    }
}
