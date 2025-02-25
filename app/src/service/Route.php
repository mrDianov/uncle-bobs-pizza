<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\service;

use Closure;
use Exception;

class Route
{
    public function __construct(
        private string $pathRegx,
        private Closure $action,
        private ?string $method = 'GET',
    ) {}

    /** @var Closure[] */
    private array $filters = [];

    public function go(): bool
    {
        $parsedUrl = parse_url($_SERVER['REQUEST_URI']);
        $path = $parsedUrl['path'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if ($this->method && strcasecmp($this->method, $requestMethod) !== 0) {
            return false;
        }
        if (preg_match($this->pathRegx, $path, $matches)) {
            foreach ($this->filters as $filter) {
                call_user_func($filter);
            }
            if (! isset($matches)) $matches = [];
            try {
                call_user_func_array($this->action, array_slice($matches, 1));
            } catch (Exception $e) {
                http_response_code(400);
                echo $e->getMessage();
            }
            return true;
        }
        return false;
    }

    public function filter(Closure $filter) 
    {
        $this->filters[] = $filter;
        return $this;
    }
}