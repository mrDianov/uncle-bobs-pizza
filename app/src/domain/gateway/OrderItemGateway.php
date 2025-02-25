<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\domain\gateway;

use dianov\unclebobspizza\domain\entity\OrderItem;
use dianov\unclebobspizza\domain\gateway\output\OrderItems;

interface OrderItemGateway
{
    function one(int $id): ?OrderItem;
    function all(?int $orderId): ?OrderItems;
    function add(OrderItem $item): int;
}