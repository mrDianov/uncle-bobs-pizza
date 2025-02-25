<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\domain\gateway\output;

use dianov\unclebobspizza\domain\entity\OrderItem;
use Iterator;

abstract class OrderItems implements Iterator
{
    abstract function current(): ?OrderItem;
    abstract function key(): ?int;

    public function getItems() {
        return array_map(function (OrderItem $orderItem) {
            return $orderItem->item;
        }, iterator_to_array($this));
    }
}
