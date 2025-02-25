<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\service\auth;

use dianov\unclebobspizza\service\Response;

class KeyAuth
{
    public function __construct(
        private string $key,
    ) {
        $this->response = new Response(errorCode: 401);
    }

    private Response $response;

    public function checkRequest() {
        $headers = apache_request_headers();
        $headersKey = $headers["X-Auth-Key"] ?? null;
        if ($headersKey !== $this->key) {
            $this->response->error("not authenticated");
            die();
        }
    }
}