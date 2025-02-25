<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\domain\gateway;

use dianov\unclebobspizza\domain\entity\Order;
use dianov\unclebobspizza\domain\gateway\output\Orders;

interface OrderGateway
{
    function all(?bool $done = null): ?Orders;
    function one(int $id): ?Order;
    function add(Order $order): int;
    function update(Order $order): void;
}
