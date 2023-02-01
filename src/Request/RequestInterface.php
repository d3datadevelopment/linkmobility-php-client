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

namespace D3\LinkmobilityClient\Request;

use Assert\InvalidArgumentException;
use D3\LinkmobilityClient\Client;
use D3\LinkmobilityClient\RecipientsList\RecipientsListInterface;
use D3\LinkmobilityClient\ValueObject\SmsMessageInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use D3\LinkmobilityClient\Response\ResponseInterface as LMResponseInterface;

interface RequestInterface
{
    // @codeCoverageIgnoreStart
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_PATCH = 'PATCH';
    public const METHOD_DELETE = 'DELETE';

    public const CONTENTTYPE_JSON = 'application/json';

    public const CONTENTCATEGORY_INFORMATIONAL = 'informational';
    public const CONTENTCATEGORY_ADVERTISEMENT = 'advertisement';

    public const MESSAGETYPE_DEFAULT = 'default';
    public const MESSAGETYPE_VOICE = 'voice';

    public const SENDERADDRESSTYPE_NATIONAL = 'national';
    public const SENDERADDRESSTYPE_INTERNATIONAL = 'international';
    public const SENDERADDRESSTYPE_ALPHANUMERIC = 'alphanumeric';
    public const SENDERADDRESSTYPE_SHORTCODE = 'shortcode';
    // @codeCoverageIgnoreEnd

    /**
     * @param SmsMessageInterface $message
     * @param Client              $client
     */
    public function __construct(SmsMessageInterface $message, Client $client);

    /**
     * @param string $method
     *
     * @return Request
     */
    public function setMethod(string $method): Request;

    /**
     * Must return the HTTP verb for this request, i.e. GET, POST, PUT
     *
     * @return string
     */
    public function getMethod(): string;

    /**
     * @param bool $test
     *
     * @return Request
     */
    public function setTestMode(bool $test): Request;

    /**
     * @return bool
     */
    public function getTestMode(): bool;

    /**
     * Must return the URI for the request with a leading slash, i.e. /messages.json
     *
     * @return string
     */
    public function getUri(): string;

    /**
     * Must return the body which is being sent as json
     *
     * @return array
     */
    public function getBody(): array;

    /**
     * Must return the class to where the response is handed over. It must implement the ResponseInterface
     *
     * @return string
     */
    public function getResponseClass(): string;

    /**
     * @param PsrResponseInterface $rawResponse
     *
     * @return LMResponseInterface
     */
    public function getResponseInstance(PsrResponseInterface $rawResponse): LMResponseInterface;

    /**
     * @return RecipientsListInterface
     */
    public function getRecipientsList(): RecipientsListInterface;

    /**
     * Must return the options for this request. If there are none, return [] (empty array)
     *
     * @return array
     */
    public function getOptions(): array;

    /**
     * Must validate the input of the request
     * This is called before sending the request
     * Must throw an exception if the validation fails
     *
     * @throws InvalidArgumentException
     */
    public function validate();
}
