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

use D3\LinkmobilityClient\Exceptions\ApiException;
use D3\LinkmobilityClient\Exceptions\ExceptionMessages;
use D3\LinkmobilityClient\Request\RequestInterface;
use D3\LinkmobilityClient\Url\Url;
use D3\LinkmobilityClient\Url\UrlInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
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
        $this->requestClient = $client ?: new \GuzzleHttp\Client([ 'base_uri' => $this->apiUrl->getBaseUri() ]);
    }

    /**
     * @param RequestInterface $request
     *
     * @return Response\ResponseInterface
     * @throws ApiException
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
     * @throws ApiException
     * @throws GuzzleException
     */
    protected function rawRequest($url, string $method = RequestInterface::METHOD_GET, array $options = []): ResponseInterface
    {
        $options['headers']['Authorization'] = 'Bearer '.$this->accessToken;

        $this->getLoggerHandler()->getLogger()->debug('linkmobility request: '.$url, $options);

        $response = $this->requestClient->request(
            $method,
            $url,
            $options
        );

        if ($response->getStatusCode() != 200) {
            $message = sprintf(ExceptionMessages::NOK_REQUEST_RETURN, $url, $response->getStatusCode());
            $response->getBody()->rewind();
            $this->getLoggerHandler()->getLogger()->error($message, [$response->getBody()->getContents()]);
            throw new ApiException($message);
        }

        $response->getBody()->rewind();
        $this->getLoggerHandler()->getLogger()->debug('response', [$response->getBody()->getContents()]);

        return $response;
    }

    /**
     * @return LoggerHandler
     */
    public function getLoggerHandler(): LoggerHandler
    {
        return LoggerHandler::getInstance();
    }
}
