<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\domain\interactor;

use dianov\unclebobspizza\domain\boundary\Interactor;
use dianov\unclebobspizza\domain\boundary\Presenter;
use dianov\unclebobspizza\domain\gateway\OrderGateway;

class ShowOrders implements Interactor
{
    public function __construct(
        private OrderGateway $orderGateway,
        private Presenter $presenter,
        private ?bool $done = null,
    ) {}

    public function interact(): void
    {
        $orders = $this->orderGateway->all($this->done);
        $result = [];
        if ($orders) {
            foreach ($orders as $order) {
                $result[] = [
                    "order_id" => strval($order->id),
                    "done" => $order->isDone(),
                ];
            }
        }
        $this->presenter->present($result);
    }
}
