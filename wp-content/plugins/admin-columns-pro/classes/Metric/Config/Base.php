<?php

declare(strict_types=1);

namespace ACP\Metric\Config;

use ACP\Metric;
use ACP\Metric\AggregationFactory;
use ACP\Metric\Type\AggregationType;
use ACP\Query\Bindings;
use ACP\Sql\AliasGenerator;

abstract class Base implements Metric\Config
{
    final public function create_query_bindings(AggregationType $aggregation_type): Bindings
    {
        $alias = AliasGenerator::generate_from_class(static::class);
        $bindings = new Bindings();

        $this->configure_query_bindings($aggregation_type, $bindings, $alias);

        AggregationFactory::create($aggregation_type)->apply(
            $bindings,
            $this->get_column($alias)
        );

        return $bindings;
    }

    protected function configure_query_bindings(
        AggregationType $aggregation_type,
        Bindings $bindings,
        string $alias
    ): void {
    }

    abstract protected function get_column(string $alias): string;

}
