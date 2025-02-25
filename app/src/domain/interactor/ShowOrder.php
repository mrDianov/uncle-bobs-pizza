<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\domain\interactor;

use dianov\unclebobspizza\domain\boundary\Interactor;
use dianov\unclebobspizza\domain\boundary\Presenter;
use dianov\unclebobspizza\domain\entity\OrderItem;
use dianov\unclebobspizza\domain\gateway\OrderGateway;
use dianov\unclebobspizza\domain\gateway\OrderItemGateway;

class ShowOrder implements Interactor
{
    public function __construct(
        private OrderGateway $orderGateway,
        private OrderItemGateway $orderItemGateway,
        private Presenter $presenter,
        private int $orderId,
    ) {}

    public function interact(): void
    {
        $order = $this->orderGateway->one($this->orderId);
        if (!$order) {
            $this->presenter->error("not found");
            return;
        }
        $items = $this->orderItemGateway->all($this->orderId)->getItems();
        $this->presenter->present([
            "order_id" => strval($order->id),
            "items" => $items,
            "done" => $order->isDone(),
        ]);
    }
}
