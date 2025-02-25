<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\data\adapter\memory;

use dianov\unclebobspizza\data\adapter\memory\output\InMemoryOrderItems;
use dianov\unclebobspizza\domain\entity\OrderItem;
use dianov\unclebobspizza\domain\gateway\OrderItemGateway;
use dianov\unclebobspizza\domain\gateway\output\OrderItems;

class InMemoryOrderItemStorage implements OrderItemGateway
{
    private int $id = 1;
    private array $ordersItems = [];

    function one(int $id): ?OrderItem
    {
        foreach ($this->ordersItems as $orderItem) {
            if ($orderItem->id === $id) return $orderItem;
        }
        return null;
    }

    function all(?int $orderId): ?OrderItems
    {
        if ($orderId !== null) {
            return new InMemoryOrderItems(
                array_filter($this->ordersItems, function(OrderItem $orderItem) use ($orderId) {
                    return $orderItem->order->id === $orderId;
                }),
            );
        }
        return new InMemoryOrderItems($this->ordersItems);
    }

    function add(OrderItem $item): int
    {
        $item->id = $this->id;
        $this->ordersItems[] = $item;
        return $this->id++;
    }
}