<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\data\adapter\postgres\output;

use dianov\unclebobspizza\domain\entity\Order;
use dianov\unclebobspizza\domain\gateway\output\Orders;
use PgSql\Connection;

class PostgresOrders implements Orders
{
    private const QUERY_STATEMENT = "orders_query";
    private const COUNT_QUERY_STATEMENT = "orders_query_count";
    
    private int $offset = 0;

    public function __construct(
        private Connection $connection,
        ?bool $done,
    ) 
    {
        pg_prepare(
            $this->connection, 
            self::QUERY_STATEMENT,
            "SELECT id, done FROM orders" 
            . ($done !== null ? " WHERE " . ($done ? "" : " NOT ") . " done " : "")
            . " OFFSET $1 LIMIT 1",
        );

        pg_prepare(
            $this->connection,
            self::COUNT_QUERY_STATEMENT,
            "SELECT COUNT(id) FROM orders"
            . ($done !== null ? " WHERE " . ($done ? "" : " NOT ") . " done " : ""),
        );
    }

    function current(): ?Order
    {
        $result = pg_execute($this->connection, self::QUERY_STATEMENT, [$this->offset]);
        if ($result) {
            $row = pg_fetch_row($result);
            return new Order(intval($row[0]), $row[1] == "t");
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
