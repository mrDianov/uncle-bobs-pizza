<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\domain\interactor;

use dianov\unclebobspizza\domain\boundary\Presenter;
use dianov\unclebobspizza\domain\boundary\Interactor;
use dianov\unclebobspizza\domain\entity\Order;
use dianov\unclebobspizza\domain\entity\OrderItem;
use dianov\unclebobspizza\domain\gateway\OrderGateway;
use dianov\unclebobspizza\domain\gateway\OrderItemGateway;
use DomainException;

class CreateOrder implements Interactor
{
    public function __construct(
        private OrderGateway $orderGateway,
        private OrderItemGateway $orderItemGateway,
        private Presenter $presenter,
        private array $items,
    ) {}

    public function interact(): void
    {
        if (! $this->items) throw new DomainException("can't create empty order");
        $order = new Order();
        $orderId = $this->orderGateway->add($order);
        $order->id = $orderId;
        foreach ($this->items as $item) {
            $this->orderItemGateway->add(new OrderItem($order, $item));
        }
        $items = $this->orderItemGateway->all($orderId)->getItems();
        $this->presenter->present([
            "order_id" => strval($orderId),
            "items" => $items,
            "done" => $order->isDone(),
        ]);
    }
}
