<?php

declare(strict_types=1);

namespace ACA\DataSources;

use AC\Collection;
use Throwable;

final class ThrowableCollection extends Collection
{
    public function add(Throwable $throwable): void
    {
        $this->data[] = $throwable;
    }

    public function current(): Throwable
    {
        return current($this->data);
    }

}
