<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\domain\interactor;

use dianov\unclebobspizza\domain\boundary\Interactor;
use dianov\unclebobspizza\domain\boundary\Presenter;
use dianov\unclebobspizza\domain\entity\OrderItem;
use dianov\unclebobspizza\domain\gateway\OrderGateway;
use dianov\unclebobspizza\domain\gateway\OrderItemGateway;

class AddItemsToOrder implements Interactor
{
    public function __construct(
        private OrderGateway $orderGateway,
        private OrderItemGateway $orderItemGateway,
        private Presenter $presenter,
        private int $orderId,
        private array $items,
    ) {}

    public function interact(): void
    {
        $order = $this->orderGateway->one($this->orderId);
        if (! $order) {
            $this->presenter->error("order {$this->orderId} not found");
            return;
        }
        if ($order->isDone()) {
            $this->presenter->error("unable to add items for done order");
            return;
        }
        foreach ($this->items as $item) {
            $this->orderItemGateway->add(new OrderItem($order, $item));
        }
        $this->presenter->present();
    }
}
