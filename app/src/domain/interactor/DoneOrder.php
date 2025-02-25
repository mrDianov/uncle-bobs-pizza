<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\domain\interactor;

use dianov\unclebobspizza\domain\boundary\Interactor;
use dianov\unclebobspizza\domain\boundary\Presenter;
use dianov\unclebobspizza\domain\gateway\OrderGateway;
use DomainException;

class DoneOrder implements Interactor
{
    public function __construct(
        private OrderGateway $orderGateway,
        private Presenter $presenter,
        private int $orderId,
    ) {}

    public function interact(): void
    {
        $order = $this->orderGateway->one($this->orderId);
        if (!$order) {
            $this->presenter->error();
            return;
        }
        try {
            $order->setDone();
            $this->orderGateway->update($order);
        } catch (DomainException $e) {
            $this->presenter->error($e->getMessage());
        }
        $this->presenter->present();
    }
}
