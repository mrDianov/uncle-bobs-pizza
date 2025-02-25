<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\service;

use dianov\unclebobspizza\domain\boundary\Presenter;

class Response implements Presenter
{
    public function __construct(
        public readonly int $successCode = 200,
        public readonly int $errorCode = 400,
    ) {}

    public function present(?array $data = null)
    {
        http_response_code($this->successCode);
        echo json_encode($data ?? "");
        // die();
    }

    public function error(?string $message = null)
    {
        http_response_code($this->errorCode);
        echo $message ?? "";
        // die();
    }
}
