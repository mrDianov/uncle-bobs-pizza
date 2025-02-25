<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\service;


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
                echo http_response_code();
            },
            method: null,
        );
    }

    private Route $notFoundRoute; 

    public function run()
    {
        foreach ($this->routes as $route) {
            if ($route->go()) return;
        }
        $this->notFoundRoute->go();
    }
}
