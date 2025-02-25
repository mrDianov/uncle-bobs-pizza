<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\domain\entity;

use DomainException;

class OrderItem
{
    public function __construct(
        public Order $order,
        public $item,
        public ?int $id = null, 
    ) {
        if (! is_int($this->item)) 
            throw new DomainException("item must be int");
        if ($this->item < 1 || $this->item > 5000) 
            throw new DomainException("item must be in [1; 5000]");
        if (! $this->order->id)
            throw new DomainException("order is not already created");
    }

}