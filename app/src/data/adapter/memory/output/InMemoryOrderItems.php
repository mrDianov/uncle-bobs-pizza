<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\data\adapter\memory\output;

use dianov\unclebobspizza\domain\entity\OrderItem;
use dianov\unclebobspizza\domain\gateway\output\OrderItems;

class InMemoryOrderItems extends OrderItems
{
    private array $orderItems;

    public function __construct(
        array $orderItems,
    ) 
    {
        $this->orderItems = array_values($orderItems);
    }

    function current(): ?OrderItem
    {
        if ($this->valid()) return $this->orderItems[$this->currentKey];
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
        return ($this->currentKey < sizeof($this->orderItems));
    }

    public function rewind(): void
    {
        $this->currentKey = 0;
    }

    private int $currentKey = 0;
}
