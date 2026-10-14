<?php

declare(strict_types=1);

namespace ACP\Sorting\Model;

use ACP\Query\Bindings;
use ACP\Sorting\Type\Order;
use ACP\Sql\AliasGenerator;

abstract class Base implements QueryBindings
{
    final public function create_query_bindings(Order $order): Bindings
    {
        $bindings = new Bindings();
        $alias = AliasGenerator::generate_from_class(static::class);

        $this->configure_query_bindings($bindings, $alias, $order);

        return $bindings;
    }

    abstract protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void;

}
