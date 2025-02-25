<?php

declare(strict_types=1);

namespace dianov\unclebobspizza\domain\gateway\output;

use dianov\unclebobspizza\domain\entity\Order;
use Iterator;

interface Orders extends Iterator
{
    function current(): ?Order;
    function key(): ?int;
}
