<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\domain\entity;

use DomainException;

class Order
{
    public function __construct(
        public ?int $id = null,
        private bool $done = false,   
    ) {}

    /**
     * @throws DomainException
     */
    public function setDone(): void
    {
        if ($this->isDone()) throw new DomainException("this order is already done");
        $this->done = true;
    }

    public function isDone(): bool
    {
        return $this->done;
    }
}
