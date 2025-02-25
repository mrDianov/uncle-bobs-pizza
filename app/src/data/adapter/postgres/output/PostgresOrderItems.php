<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\data\adapter\postgres\output;

use dianov\unclebobspizza\data\adapter\postgres\PostgresOrderStorage;
use dianov\unclebobspizza\domain\entity\OrderItem;
use dianov\unclebobspizza\domain\gateway\output\OrderItems;
use PgSql\Connection;

class PostgresOrderItems extends OrderItems
{
    private const QUERY_STATEMENT = "order_items_query";
    private const COUNT_QUERY_STATEMENT = "order_items_query_count";

    private int $offset = 0;

    public function __construct(
        private Connection $connection,
        private ?int $orderId = null,
    )
    {
        pg_prepare(
            $this->connection, 
            self::QUERY_STATEMENT,
            "SELECT id, order_id, item FROM order_items" 
            . ($orderId !== null ? " WHERE order_id = {$this->orderId} " : "")
            . " OFFSET $1 LIMIT 1",
        );

        pg_prepare(
            $this->connection,
            self::COUNT_QUERY_STATEMENT,
            "SELECT COUNT(id) FROM order_items"
            . ($orderId !== null ? " WHERE order_id = {$this->orderId} " : "")
        );
    }

    function current(): ?OrderItem
    {
        $result = pg_execute($this->connection, self::QUERY_STATEMENT, [$this->offset]);
        if ($result) {
            $row = pg_fetch_row($result);
            $id = intval($row[0]);
            $item = intval($row[2]);
            $order = (new PostgresOrderStorage($this->connection))->one(intval($row[1]));
            if ($order) return new OrderItem($order, $item, $id);
        }
        return null;
    }
    
    function key(): int
    {
        return $this->offset;
    }

    public function next(): void
    {
        if ($this->valid()) {
            $this->offset++;
        }
    }

    public function valid(): bool
    {
        $result = pg_execute($this->connection, self::COUNT_QUERY_STATEMENT, []);
        $count = intval(pg_fetch_row($result)[0]);
        return ($this->offset < $count);
    }

    public function rewind(): void
    {
        $this->offset = 0;
    }
}