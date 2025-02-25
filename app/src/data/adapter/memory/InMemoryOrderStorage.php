<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\data\adapter\memory;

use dianov\unclebobspizza\data\adapter\memory\output\InMemoryOrders;
use dianov\unclebobspizza\domain\entity\Order;
use dianov\unclebobspizza\domain\gateway\OrderGateway;
use dianov\unclebobspizza\domain\gateway\output\Orders;

class InMemoryOrderStorage implements OrderGateway
{
    private array $orders = [];

    private int $id = 1;

    public function all(?bool $done = null): ?Orders
    {
        if ($this->orders) {
            if ($done !== null) {
                return new InMemoryOrders(
                    array_filter(
                        $this->orders, 
                        function(Order $order) use ($done) {
                            return $order->isDone() === $done;
                        }
                    ),
                );
            }
            return new InMemoryOrders($this->orders);
        }
        return null;
    }

    public function one(int $id): ?Order
    {
        foreach ($this->orders as $order) {
            if ($order->id === $id) return $order;
        }
        return null;
    }
    
    public function add(Order $order): int
    {
        $order->id = $this->id;
        $this->orders[] = $order;
        return $this->id++;
    }

    public function update(Order $order): void
    {
        // заказ уже в памяти
        return;
    }
}