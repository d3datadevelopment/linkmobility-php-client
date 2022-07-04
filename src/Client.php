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

namespace D3\LinkmobilityClient;

use D3\LinkmobilityClient\Exceptions\ApiException;
use D3\LinkmobilityClient\Exceptions\ExceptionMessages;
use D3\LinkmobilityClient\Request\RequestInterface;
use D3\LinkmobilityClient\ValueObject\ValueObject;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use phpDocumentor\Reflection\Types\Mixed_;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

class Client
{
    private $accessToken;
    public $apiUrl;
    public $requestClient;

    private $logger;
    private $configuration = [];

    public function __construct(string $accessToken, $apiUrl = false, $client = false)
    {
        if ($apiUrl !== false && false === $apiUrl instanceof UrlInterface) {
            throw new RuntimeException(ExceptionMessages::WRONG_APIURL_INTERFACE);
        }

        $this->accessToken = $accessToken;
        $this->apiUrl = $apiUrl ?: new Url();
        $this->requestClient = $client ?: new \GuzzleHttp\Client( [ 'base_uri' => $this->apiUrl->getBaseUri() ] );
    }

    /**
     * @param RequestInterface $request
     *
     * @return Response\ResponseInterface
     * @throws ApiException
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function request(RequestInterface $request) : Response\ResponseInterface
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
    protected function rawRequest( $url, string $method = RequestInterface::METHOD_GET, array $options = []): ResponseInterface
    {
        $options['headers']['Authorization'] = 'Bearer '.$this->accessToken;

        if ($this->hasLogger()) $this->getLogger()->debug('request '.$url, $options);

        $response = $this->requestClient->request(
            $method,
            $url,
            $options
        );

        if ($response->getStatusCode() != 200) {
            $message = sprintf(ExceptionMessages::NOK_REQUEST_RETURN, [$url, $response->getStatusCode()]);
            if ($this->hasLogger()) $this->getLogger()->error($message);
            throw new ApiException($message);
        }

        if ($this->hasLogger()) {
            $response->getBody()->rewind();
            $this->getLogger()->debug('response', [$response->getBody()->getContents()]);
        }

        return $response;
    }

    /**
     * @param mixed $logger
     *
     * @return Client
     */
    public function setLogger(LoggerInterface $logger ) : Client
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasLogger() : bool
    {
        return $this->logger instanceof LoggerInterface;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param string $name
     * @param        $configuration
     *
     * @return $this
     */
    public function setConfiguration( string $name, $configuration ): Client
    {
        $this->configuration[$name] = oxNew(ValueObject::class, $configuration);

        return $this;
    }

    public function hasConfiguration(string $name)
    {
        return isset($this->configuration[$name]);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getConfiguration(string $name)
    {
        if (false === isset($this->configuration)) {
            throw new InvalidArgumentException('configuration '.$name.' is not set');
        }

        return $this->configuration[$name];
    }
}