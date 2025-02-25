<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\domain\boundary;

interface Presenter {
    function present(?array $data = null);
    function error(?string $message = null);
}