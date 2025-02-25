<?php

declare(strict_types=1);

use dianov\unclebobspizza\data\adapter\postgres\PostgresOrderItemStorage;
use dianov\unclebobspizza\data\adapter\postgres\PostgresOrderStorage;
use dianov\unclebobspizza\data\database\postgres\Database;
use dianov\unclebobspizza\domain\interactor\AddItemsToOrder;
use dianov\unclebobspizza\domain\interactor\CreateOrder;
use dianov\unclebobspizza\domain\interactor\DoneOrder;
use dianov\unclebobspizza\domain\interactor\ShowOrder;
use dianov\unclebobspizza\domain\interactor\ShowOrders;
use dianov\unclebobspizza\service\auth\KeyAuth;
use dianov\unclebobspizza\service\Response;
use dianov\unclebobspizza\service\Route;
use dianov\unclebobspizza\service\Router;

require "../vendor/autoload.php";


$environment = parse_ini_file(".env");

// зависимости
$createResponse = new Response();
$foundResponse = new Response(errorCode: 404);

$auth = new KeyAuth($environment["API_KEY"]);
$db = new Database(
    host: $environment["DB_HOST"],
    port: intval($environment["DB_PORT"]),
    dbname: $environment["DB_NAME"],
    user: $environment["DB_USER"],
    password: $environment["DB_PASSWORD"],
);
$connection = $db->connect();

// $ordersStorage = new InMemoryOrderStorage();
// $orderItemsStorage = new InMemoryOrderItemStorage();
$ordersStorage = new PostgresOrderStorage($connection);
$orderItemsStorage = new PostgresOrderItemStorage($connection);

// маршруты
$router = new Router([
    (new Route("/^\/orders\/?$/",
        function () use ($ordersStorage, $foundResponse) {
            $done = $_GET["done"] ?? null;
            $boolDone = null;
            if ($done === "0") {
                $boolDone = false;
            } else if ($done === "1") {
                $boolDone = true;
            } else if ($done && $done > 1) {
                /* здесь можно что-то сделать с невалидным значением валидного параметра,
                но это зависит от подхода конкретной команды, 
                и лично я предпочёл игнорировать данный случай */
            }
            $action = new ShowOrders(
                $ordersStorage,
                $foundResponse,
                $boolDone,
            );
            $action->interact();
         },
    ))->filter(fn() => $auth->checkRequest()),
    new Route("/^\/orders\/(\d*)\/?$/",
        function (int $orderId) use ($ordersStorage, $orderItemsStorage, $foundResponse) {
            $action = new ShowOrder(
                $ordersStorage,
                $orderItemsStorage,
                $foundResponse,
                $orderId,
            );
            $action->interact();
        },
    ),
    new Route("/^\/orders\/?$/",
        function () use ($ordersStorage, $orderItemsStorage, $createResponse) {
            $items = json_decode(file_get_contents("php://input"), true)["items"] ?? [];
            
            $action = new CreateOrder(
                $ordersStorage,
                $orderItemsStorage,
                $createResponse,
                $items,
            );
            $action->interact();
        },
        method: "POST"
    ),
    new Route("/^\/orders\/(\d*)\/items\/?$/",
        function (int $orderId) use ($ordersStorage, $orderItemsStorage, $foundResponse) {
            $items = json_decode(file_get_contents("php://input")) ?? [];
            $action = new AddItemsToOrder(
                $ordersStorage,
                $orderItemsStorage,
                $foundResponse,
                $orderId,
                $items,
            );
            $action->interact();
        },
        method: "POST"
    ),
    (new Route("/^\/orders\/(\d*)\/done\/?$/",
        function (int $orderId) use ($ordersStorage, $foundResponse) {
            $action = new DoneOrder(
                $ordersStorage,
                $foundResponse,
                $orderId,
            );
            $action->interact();
        },
        method: "POST"
    ))->filter(fn() => $auth->checkRequest()),
]);

// выполнение маршрутизации
$router->run();

// закрываем соединение с БД
pg_close($connection);