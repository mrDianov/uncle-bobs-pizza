<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\service;

use Throwable;

class Router
{
    /**
     * @param Route[] $routes
     */
    public function __construct(
        private array $routes,
    ) {
        $this->notFoundRoute = new Route(
            "/^(.*)$/",
            function (string $path) {
                $response = new Response(errorCode: 404);
                $response->error("method: '$path' not found");
            },
            method: null,
        );
        $this->internalErrorRoute = new Route(
            "/^(.*)$/",
            function (string $path) {
                $response = new Response(errorCode: 500);
                $response->error("internal server error");
            },
            method: null,
        );
    }

    private Route $notFoundRoute;
    private Route $internalErrorRoute;

    public function run()
    {
        try {
            foreach ($this->routes as $route) {
                if ($route->go()) return;
            }
            $this->notFoundRoute->go();
        } catch (Throwable $e) {
            $this->internalErrorRoute->go();
        }
    }
}
