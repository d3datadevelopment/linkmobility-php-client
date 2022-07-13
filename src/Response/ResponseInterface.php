<?php

declare(strict_types=1);

namespace D3\LinkmobilityClient\Response;

interface ResponseInterface
{
    public function __construct(\Psr\Http\Message\ResponseInterface $rawResponse);

    public function getRawResponse(): \Psr\Http\Message\ResponseInterface;

    public function getInternalStatus(): int;

    public function getStatusMessage(): string;

    public function getClientMessageId();

    public function getTransferId();

    public function getSmsCount(): int;

    public function isSuccessful(): bool;

    public function getErrorMessage(): string;
}
