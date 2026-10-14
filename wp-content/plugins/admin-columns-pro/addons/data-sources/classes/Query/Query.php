<?php

declare(strict_types=1);

namespace ACA\DataSources\Query;

use ACP;

class Query extends ACP\Query
{
    public function register(): void
    {
        add_filter('acp/data-sources/bindings', [$this, 'register_bindings']);
    }

    public function register_bindings($bindings): array
    {
        foreach ($this->bindings as $binding) {
            $bindings[] = $binding;
        }

        return $bindings;
    }

}
