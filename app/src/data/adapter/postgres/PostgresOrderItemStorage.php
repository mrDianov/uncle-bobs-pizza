<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\data\adapter\postgres;

use dianov\unclebobspizza\data\adapter\postgres\output\PostgresOrderItems;
use dianov\unclebobspizza\domain\entity\OrderItem;
use dianov\unclebobspizza\domain\gateway\OrderItemGateway;
use dianov\unclebobspizza\domain\gateway\output\OrderItems;
use Exception;
use PgSql\Connection;

class PostgresOrderItemStorage implements OrderItemGateway
{
    public function __construct(
        private Connection $connection
    ) {}

    function one(int $id): ?OrderItem
    {
        $result = pg_query_params(
            $this->connection,
            "SELECT id, order_id, item FROM order_items WHERE id = $1",
            [$id]
        );
        if ($result) {
            $row = pg_fetch_row($result);
            $item = intval($row[2]);
            $order = (new PostgresOrderStorage($this->connection))->one($row[1]);
            if ($order) return new OrderItem($order, $item, $id);
        }
        return null;
    }

    function all(?int $orderId): ?OrderItems
    {
        return new PostgresOrderItems($this->connection, $orderId);
    }

    function add(OrderItem $item): int
    {
        $result = pg_query_params(
            $this->connection, 
            "INSERT INTO order_items (order_id, item) VALUES ($1, $2) RETURNING id", 
            [$item->order->id, $item->item]
        );
        if ($result) {
            return intval(pg_fetch_row($result)[0]);
        }
        throw new Exception("unable to save order item");
    }
}