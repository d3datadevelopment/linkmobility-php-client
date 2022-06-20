<?php 

declare(strict_types=1);

namespace D3\LinkmobilityClient\Response;

use Assert\Assert;

abstract class Response implements ResponseInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var int
     */
    protected $status;

    public function __construct(array $data)
    {
        Assert::that($data)->keyExists('status');

        $this->data = $data;
        $this->status = (int)$this->data['status'];

        if ($this->isSuccessful()) {
            $this->init();
        }
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }

    public function getError(): string
    {
        return $this->isSuccessful() ? '' : $this->data['message'];
    }
}
