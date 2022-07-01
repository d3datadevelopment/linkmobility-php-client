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

declare(strict_types=1);

namespace D3\LinkmobilityClient\Request;

use D3\LinkmobilityClient\Client;
use D3\LinkmobilityClient\ValueObject\SmsMessageInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use D3\LinkmobilityClient\Response\ResponseInterface as LMResponseInterface;

interface RequestInterface
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';

    const CONTENTTYPE_JSON = 'application/json';

    const CONTENTCATEGORY_INFORMATIONAL = 'informational';
    const CONTENTCATEGORY_ADVERTISEMENT = 'advertisement';

    const MESSAGETYPE_DEFAULT = 'default';
    const MESSAGETYPE_VOICE = 'voice';

    const SENDERADDRESSTYPE_NATIONAL = 'national';
    const SENDERADDRESSTYPE_INTERNATIONAL = 'international';
    const SENDERADDRESSTYPE_ALPHANUMERIC = 'alphanumeric';
    const SENDERADDRESSTYPE_SHORTCODE = 'shortcode';

    public function __construct(SmsMessageInterface $message, Client $client);

    public function setMethod(string $method);

    /**
     * Must return the HTTP verb for this request, i.e. GET, POST, PUT
     *
     * @return string
     */
    public function getMethod() : string;

    /**
     * Must return the URI for the request with a leading slash, i.e. /messages.json
     *
     * @return string
     */
    public function getUri() : string;

    /**
     * Must return the body which is being sent as json
     *
     * @return array
     */
    public function getBody() : array;

    /**
     * Must return the class to where the response is handed over. It must implement the ResponseInterface
     *
     * @return string
     */
    public function getResponseClass() : string;

    /**
     * @param PsrResponseInterface $rawResponse
     *
     * @return LMResponseInterface
     */
    public function getResponseInstance(PsrResponseInterface $rawResponse): LMResponseInterface;

    /**
     * Must return the options for this request. If there are none, return [] (empty array)
     *
     * @return array
     */
    public function getOptions() : array;

    /**
     * Must validate the input of the request
     * This is called before sending the request
     * Must throw an exception if the validation fails
     *
     * @throws InvalidArgumentException
     */
    public function validate();
}