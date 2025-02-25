<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\data\adapter\postgres;

use dianov\unclebobspizza\data\adapter\postgres\output\PostgresOrders;
use dianov\unclebobspizza\domain\entity\Order;
use dianov\unclebobspizza\domain\gateway\OrderGateway;
use dianov\unclebobspizza\domain\gateway\output\Orders;
use Exception;
use PgSql\Connection;

class PostgresOrderStorage implements OrderGateway
{
    public function __construct(
        private Connection $connection,
    ) {}

    public function all(?bool $done = null): ?Orders
    {
        return new PostgresOrders($this->connection, $done);
    }

    public function one(int $id): ?Order
    {
        $result = pg_query_params(
            $this->connection,
            "SELECT id, done FROM orders WHERE id = $1",
            [$id]
        );
        if ($result) {
            $row = pg_fetch_row($result);
            return new Order(intval($row[0]), $row[1] == "t");
        }
        return null;
    }

    public function add(Order $order): int
    {
        $result = pg_query_params(
            $this->connection, 
            "INSERT INTO orders (done) VALUES ($1) RETURNING id", 
            [$order->isDone() ? "true" : "false"]
        );
        if ($result) {
            return intval(pg_fetch_row($result)[0]);
        }
        throw new Exception("unable to save order");
    }

    public function update(Order $order): void
    {
        $result = pg_query_params(
            $this->connection, 
            "UPDATE orders SET done = $1 WHERE id = $2", 
            [$order->isDone() ? "true" : "false", $order->id]
        );
        if ($result) return;
        throw new Exception("unable to update order");
    }
}
