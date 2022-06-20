<?php

declare(strict_types=1);

namespace D3\LinkmobilityClient\ValueObject;

use Assert\Assert;

class Sender extends StringValueObject
{
    public function __construct(string $value)
    {
        // if the sender is alphanumeric, test the length
        if (!preg_match('/^\+[0-9]+$/i', $value)) {
            Assert::that($value)->maxLength(11);
        }

        parent::__construct($value);
    }
}
