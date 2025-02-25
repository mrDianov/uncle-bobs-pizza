<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\domain\boundary;

interface Interactor
{
    function interact(): void;
}