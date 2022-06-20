<?php

declare(strict_types=1);

namespace D3\LinkmobilityClient\Response;

interface ResponseInterface
{
    /**
     * Should instantiate the object from the data given
     */
    public function init() : void;

    public function __construct(array $data);

    /**
     * Must return true if the request was successful
     *
     * @return bool
     */
    public function isSuccessful() : bool;

    /**
     * This must return the error, if any occurred, else it must return ''
     *
     * @return string
     */
    public function getError() : string;
}
