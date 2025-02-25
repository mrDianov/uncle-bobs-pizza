<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\data\adapter\memory\output;

use dianov\unclebobspizza\domain\entity\Order;
use dianov\unclebobspizza\domain\gateway\output\Orders;

class InMemoryOrders implements Orders
{
    private array $orders;

    public function __construct(
        array $orders,
    ) 
    {
        $this->orders = array_values($orders);
    }

    function current(): ?Order
    {
        if ($this->valid()) return $this->orders[$this->currentKey];
        return null;
    }
    
    function key(): int
    {
        return $this->currentKey;
    }

    public function next(): void
    {
        if ($this->valid()) {
            $this->currentKey++;
        }
    }

    public function valid(): bool
    {
        return ($this->currentKey < sizeof($this->orders));
    }

    public function rewind(): void
    {
        $this->currentKey = 0;
    }

    private int $currentKey = 0;
}
